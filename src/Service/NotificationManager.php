<?php

namespace App\Service;

use App\Entity\Citation;
use App\Entity\Comment;
use App\Entity\Locution;
use App\Entity\Mot;
use App\Entity\Proverbe;
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
     * NotificationManager constructor.
     *
     * @param ContainerInterface $container
     * @param Swift_Mailer $mailer
     * @param Twig\Environment $twig
     * @param TranslatorInterface $translator
     */
    public function __construct(ContainerInterface $container, Swift_Mailer $mailer, Twig\Environment $twig, TranslatorInterface $translator)
    {
        $this->container = $container;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    /**
     * @param array $recipients
     * @param Mot|Locution|Proverbe|Citation $post
     * @param string $permalink
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function send(array $recipients, $post, string $permalink): void
    {
        //Do not notify if the recipients are empty - for example when the post owner comments his post
        if (empty($recipients)) {
            return;
        }

        $recipients = \implode(',', $recipients);
        $recipients = \trim($recipients, ',');
        $appMailer = $this->container->getParameter('app_notifier_email');
        $appName = $this->container->getParameter('app_name');

        $messageToOwner = (new Swift_Message($this->translator->trans('email.publisher.subject')))
            ->setFrom($appMailer, $appName)
            ->setTo($recipients)
            ->setBody($this->twig->render('emails/comment__to-owner.html.twig', [
                    'post'  => $post, 'post_url' => $permalink]
            ), 'text/html');

        $messageToParticipants = (new Swift_Message($this->translator->trans('email.commenter.subject')))
            ->setFrom($appMailer, $appName)
            ->setTo($recipients)
            ->setBody($this->twig->render('emails/comment__to-recipients.html.twig', [
                'post'  => $post, 'post_url' => $permalink]
            ), 'text/html');

        $this->mailer->send($messageToOwner);
        $this->mailer->send($messageToParticipants);
    }

}