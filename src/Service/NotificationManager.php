<?php

namespace App\Service;

use App\Entity\Joke;
use App\Entity\Comment;
use App\Entity\Expression;
use App\Entity\Word;
use App\Entity\Proverb;
use App\Utils\ModelUtils;
use App\Utils\PhpUtils;
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
     * @var RouterInterface
     */
    private $router;

    /**
     * NotificationManager constructor.
     * @param ContainerInterface $container
     * @param Swift_Mailer $mailer
     * @param Twig\Environment $twig
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     */
    public function __construct(
        ContainerInterface $container,
        Swift_Mailer $mailer,
        Twig\Environment $twig,
        TranslatorInterface $translator,
        RouterInterface $router)
    {
        $this->container = $container;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param Word|Expression|Proverb|Joke $post
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     * @throws \ReflectionException
     */
    public function send($post): void
    {
        $entity = \strtolower(PhpUtils::getClassName($post));
        $permalink = $this->router->generate('post_show', [
            'domain' => ModelUtils::getDomainByEntity($entity),
            'id' => $post->getId(),
            'slug' => $post->getSlug()
        ], RouterInterface::ABSOLUTE_URL);

        $recipients = [];

        /** @var Comment $comment */
        foreach ($post->getComments() as $comment) {
            $email = $comment->getUser()->getEmail();

            if (!\in_array($email, $recipients)) {
                \array_push($recipients, $email);
            }
        }

        //Remove post owner from recipients
        $recipients = \array_filter($recipients, function($e) use ($post) {
            return ($e !== $post->getUser()->getEmail());
        });

        //Do not notify if the recipients are empty - for example when the post owner comments his post
        if (empty($recipients)) {
            return;
        }

        $appMailer = $this->container->getParameter('app_notifier_email');
        $appName = $this->container->getParameter('app_name');

        $messageToOwner = (new Swift_Message($this->translator->trans('email.publisher.subject')))
            ->setFrom($appMailer, $appName)
            ->setTo($post->getUser()->getEmail())
            ->setBody($this->twig->render('emails/comment__to-owner.html.twig', [
                    'post'  => $post, 'post_url' => $permalink]
            ), 'text/html');

        $messageToParticipants = (new Swift_Message($this->translator->trans('email.commenter.subject')))
            ->setFrom($appMailer, $appName)
            ->setTo($appMailer)
            ->setBcc($recipients)
            ->setBody($this->twig->render('emails/comment__to-recipients.html.twig', [
                'post'  => $post, 'post_url' => $permalink]
            ), 'text/html');

        $this->mailer->send($messageToOwner);
        $this->mailer->send($messageToParticipants);
    }

}