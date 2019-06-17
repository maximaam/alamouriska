<?php

namespace App\Command;

use App\Entity\AbstractPost;
use App\Entity\CommentMailQueue;
use App\Entity\Expression;
use App\Entity\Joke;
use App\Entity\LatestPosts;
use App\Entity\Proverb;
use App\Entity\Word;
use App\Service\NotificationManager;
use App\Utils\ModelUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UpdateLatestPostsCommand extends Command
{
    protected static $defaultName = 'app:update-latest-posts';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NotificationManager
     */
    private $notificationManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(EntityManagerInterface $entityManager, NotificationManager $notificationManager, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->notificationManager = $notificationManager;
        $this->urlGenerator = $urlGenerator;
    }

    protected function configure()
    {
        $this->setDescription('Updates the last added posts DESC');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Starts updating the latest posts...');

        //Truncate table
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeUpdate($platform->getTruncateTableSQL($this->entityManager->getClassMetadata(LatestPosts::class)->getTableName(), true));
        $connection->query('SET FOREIGN_KEY_CHECKS=1');

        $words = $this->entityManager->getRepository(Word::class)->findBy([], ['id' => 'DESC'], 5);
        $expressions = $this->entityManager->getRepository(Expression::class)->findBy([], ['id' => 'DESC'], 5);
        $proverbs = $this->entityManager->getRepository(Proverb::class)->findBy([], ['id' => 'DESC'], 5);
        $jokes = $this->entityManager->getRepository(Joke::class)->findBy([], ['id' => 'DESC'], 5);

        $all = \array_merge($words, $expressions, $proverbs, $jokes);

        $posts = [];

        /** @var AbstractPost $obj */
        foreach ($all as $obj) {
            $posts[$obj->getCreatedAt()->getTimestamp()] = [
                'post'  => \substr($obj->getPost(), 0, 32),
                'permalink' => $this->urlGenerator->generate('post_show', [
                    'id'    => $obj->getId(),
                    'domain'    => ModelUtils::getDomainByPost($obj),
                    'slug'  => $obj->getSlug()
                ])
            ];
        }

        //Sort with timestamp DESC
        \krsort($posts);

        foreach ($posts as $timestamp => $post) {
            $latest = (new LatestPosts())
                ->setCreatedAt((new \DateTime())->setTimestamp($timestamp))
                ->setPost($post['post'])
                ->setPermalink($post['permalink'])
            ;

            $this->entityManager->persist($latest);
        }

        $this->entityManager->flush();

        $io->success('Done updating latest posts.');
    }
}
