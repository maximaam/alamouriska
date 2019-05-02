<?php

namespace App\Service;

use App\Entity\Comment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig;
use Swift_Mailer;
use Swift_Message;

/**
 * Class NotificationManager
 * @package App\Service
 */
class NotificationManager
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Twig\Environment
     */
    private $twig;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * NotificationManager constructor.
     *
     * @param ContainerInterface $container
     * @param Swift_Mailer $mailer
     * @param RouterInterface $router
     * @param Twig\Environment $twig
     * @param TranslatorInterface $translator
     */
    public function __construct(ContainerInterface $container, Swift_Mailer $mailer, RouterInterface $router, Twig\Environment $twig, TranslatorInterface $translator)
    {
        $this->container = $container;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    /**
     * @param Comment $comment
     * @param array $recipients
     * @param $post
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function send(Comment $comment, array $recipients, $post): void
    {
        //Do not notify if the recipients are empty - for example when the post owner comments his post
        if (empty($recipients)) {
            return;
        }

        $recipients = \implode(',', $recipients);
        $recipients = \trim($recipients, ',');
        $route = 'post_' . $comment->getThread()->getOwner() . '_show';
        $appMailer = $this->container->getParameter('app_notifier_email');
        $appName = $this->container->getParameter('app_name');

        $postUrl = $this->router->generate($route, ['id' => $post->getId(), 'slug' => $post->getSlug()], UrlGenerator::ABSOLUTE_URL);

        $messageToOwner = (new Swift_Message($this->translator->trans('email.publisher.subject')))
            ->setFrom($appMailer, $appName)
            ->setTo($recipients)
            ->setBody($this->twig->render('emails/comment__to-owner.html.twig', [
                    'post'  => $post, 'post_url' => $postUrl]
            ), 'text/html');

        $messageToParticipants = (new Swift_Message($this->translator->trans('email.commenter.subject')))
            ->setFrom($appMailer, $appName)
            ->setTo($recipients)
            ->setBody($this->twig->render('emails/comment__to-recipients.html.twig', [
                'post'  => $post, 'post_url' => $postUrl]
            ), 'text/html');

        $this->mailer->send($messageToOwner);
        $this->mailer->send($messageToParticipants);
    }

}