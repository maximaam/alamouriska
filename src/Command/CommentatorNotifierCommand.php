<?php

namespace App\Command;

use App\Entity\CommentMailQueue;
use App\Entity\Expression;
use App\Entity\Joke;
use App\Entity\Proverb;
use App\Entity\Word;
use App\Service\NotificationManager;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig;

/**
 * Class CommentatorNotifierCommand
 * @package App\Command
 */
class CommentatorNotifierCommand extends Command
{
    protected static $defaultName = 'app:commentator-notifier';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NotificationManager
     */
    private $notificationManager;

    public function __construct(EntityManagerInterface $entityManager, NotificationManager $notificationManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->notificationManager = $notificationManager;
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Sends an email to commentators.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     * @throws DBALException
     * @throws ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var CommentMailQueue[] $queues */
        $queues = $this->entityManager->getRepository(CommentMailQueue::class)->findAll();

        $io->note(\sprintf('Starting notifications. Found %d case(s) in the queue', \count($queues)));

        foreach ($queues as $queue) {

            /** @var Word|Expression|Proverb|Joke $post */
            $post = $this->entityManager->find('App\\Entity\\' . $queue->getPost(), $queue->getPostId());

            if (null === $post || null === $post->getComments()) {
                continue;
            }

            $this->notificationManager->send($post);
        }

        $this->truncateTable($this->entityManager->getClassMetadata(CommentMailQueue::class)->getTableName());

        $io->success('Finished.');
    }

    /**
     * @param $table
     * @param bool $cascade
     * @throws DBALException
     */
    private function truncateTable($table, $cascade = true): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeUpdate($platform->getTruncateTableSQL($table, $cascade));
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
