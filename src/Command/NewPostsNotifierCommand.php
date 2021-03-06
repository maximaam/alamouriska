<?php

namespace App\Command;

use App\Entity\Blog;
use App\Entity\Expression;
use App\Entity\Joke;
use App\Entity\Proverb;
use App\Entity\User;
use App\Entity\Word;
use App\Utils\ModelUtils;
use App\Utils\PhpUtils;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionException;
use Swift_Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class NewPostsNotifierCommand
 * @package App\Command
 */
class NewPostsNotifierCommand extends Command
{
    protected static $defaultName = 'app:new-posts-notifier';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * NewPostsNotifierCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param UrlGeneratorInterface $urlGenerator
     * @param Environment $twig
     * @param Swift_Mailer $mailer
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, UrlGeneratorInterface $urlGenerator, Environment $twig, Swift_Mailer $mailer)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->container = $container;
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setDescription('Sends an email to notify about new posts.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $words = $this->entityManager->getRepository(Word::class)->findBy([], ['id' => 'DESC'], 5);
        $expressions = $this->entityManager->getRepository(Expression::class)->findBy([], ['id' => 'DESC'], 5);
        $proverbs = $this->entityManager->getRepository(Proverb::class)->findBy([], ['id' => 'DESC'], 5);
        $jokes = $this->entityManager->getRepository(Joke::class)->findBy([], ['id' => 'DESC'], 5);
        $blogs = $this->entityManager->getRepository(Blog::class)->findBy([], ['id' => 'DESC'], 5);

        $all = \array_merge($words, $expressions, $proverbs, $jokes, $blogs);

        if (empty($all)) {
            exit(1);
        }

        $io->note(\sprintf('Starting notifications. Found %d case(s)', \count($all)));

        $posts = [];
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findBy(['enabled' => true, 'allowPostNotification' => true]);

        $appMailerReceiver = $this->container->getParameter('app_receiver_email');
        $recipients = [];

        foreach ($users as $user) {
            \array_push($recipients, $user->getEmail());
        }

        unset($users);

        /** @var Word|Expression|Proverb|Joke $obj */
        foreach ($all as $obj) {
            if ($obj->getCreatedAt() < (new \DateTime())->modify('-7 day')) {
                continue;
            }

            $entity = \strtolower(PhpUtils::getClassName($obj));
            $post = $obj->getPost();

            if (\strlen($post) >= 32) {
                $post = \substr($obj->getPost(), 0, 32) . '...';
            }

            $posts[] = [
                'post' => $post,
                'username' => $obj->getUser()->getUsername(),
                'permalink' => $this->container->getParameter('full_domain') . '/' . ModelUtils::getDomainByEntity($entity) . '/' . $obj->getId() . '/' . $obj->getSlug()
            ];
        }

        if (empty($posts)) {
            $io->writeln('Exit. No posts.');
            exit(1);
        }

        $message = (new \Swift_Message('Publications récentes sur ' . $this->container->getParameter('app_name')))
            ->setFrom($this->container->getParameter('app_notifier_email'), $this->container->getParameter('app_name'))
            ->setTo($appMailerReceiver)
            ->setBcc($recipients)
            ->setBody($this->twig->render('emails/notification__new-posts.html.twig', [
                'posts'  => $posts
            ]), 'text/html');

        $this->mailer->send($message);

        $io->success('Finished.');

        return 0;
    }
}
