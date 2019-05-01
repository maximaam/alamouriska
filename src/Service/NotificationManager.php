<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Locution;
use App\Entity\Mot;
use App\Entity\Proverbe;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class NotificationManager
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * NotificationManager constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, RouterInterface $router, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @param Comment $comment
     * @param array $recipients
     * @param Mot|Locution|Proverbe $post
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(Comment $comment, array $recipients, $post): void
    {
        //Do not notify if the recipients are empty - for example when the post owner comments his post
        if (empty($recipients)) {
            return;
        }

        $recipients = implode(',', $recipients);
        $recipients = trim($recipients, ',');
        $route = 'post_' . $comment->getThread()->getOwner() . '_show';

        $postUrl = $this->router->generate($route, ['id' => $post->getId(), 'slug' => $post->getSlug()], UrlGenerator::ABSOLUTE_URL);

        $messageToOwner = (new \Swift_Message('Un commentaire a été ajouté à ta publication.'))
            ->setFrom('alamouriska.app@gmail.com', 'ALAMOURISKA')
            ->setTo($recipients)
            ->setBody($this->twig->render('emails/comment__to-owner.html.twig', [
                    'post'  => $post, 'post_url' => $postUrl]
            ), 'text/html');

        $messageToParticipants = (new \Swift_Message('Nouveau commentaire sur la publication que tu suis.'))
            ->setFrom('alamouriska.app@gmail.com', 'ALAMOURISKA')
            ->setTo($recipients)
            ->setBody($this->twig->render('emails/comment__to-recipients.html.twig', [
                'post'  => $post, 'post_url' => $postUrl]
            ), 'text/html');

        $this->mailer->send($messageToOwner);
        $this->mailer->send($messageToParticipants);
    }

}