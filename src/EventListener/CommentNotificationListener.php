<?php

namespace App\EventListener;

use App\Entity\Citation;
use App\Entity\Comment;
use App\Entity\Locution;
use App\Entity\Mot;
use App\Entity\Proverbe;
use App\Entity\Thread;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CommentBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\CommentBundle\Events as FOSCommentEvents;
use FOS\CommentBundle\Model\CommentManagerInterface;
use App\Service\NotificationManager;
use Twig;

/**
 * Class CommentNotificationListener
 * @package App\EventListener
 */
class CommentNotificationListener implements EventSubscriberInterface
{
    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * @var NotificationManager
     */
    private $notificationManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CommentNotificationListener constructor.
     * @param NotificationManager $notificationManager
     * @param CommentManagerInterface $commentManager
     * @param EntityManagerInterface $em
     */
    public function __construct(NotificationManager $notificationManager, CommentManagerInterface $commentManager, EntityManagerInterface $em)
    {
        $this->notificationManager = $notificationManager;
        $this->commentManager = $commentManager;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSCommentEvents::COMMENT_POST_PERSIST => 'onCommentPostPersist',
        ];
    }

    /**
     * @param CommentEvent $event
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function onCommentPostPersist(CommentEvent $event)
    {
        /** @var Comment $comment */
        $comment = $event->getComment();

        //Only add comment should trigger
        if ($comment->getState() !== Comment::STATE_VISIBLE) {
            return;
        }

        //file_put_contents(__FILE__ . '.html', serialize($comment));

        //if (!$this->commentManager->isNewComment($comment)) {
            //return;
        //}


        /** @var Thread $thread */
        $thread = $comment->getThread();

        /** @var  Comment[] $comment */
        $comments = $this->commentManager->findCommentsByThread($thread);

        $recipients = [];

        foreach ($comments as $comment) {
            $email = $comment->getAuthor()->getEmail();

            if (!in_array($email, $recipients, true)) {
                \array_push($recipients, $email);
            }
        }

        /** @var Mot|Locution|Proverbe|Citation $post */
        $post = $this->em->getRepository($this->getPostClass($thread->getOwner()))->find($thread->getOwnerId());

        //Remove post owner from recipients as they get an extra email
        //$recipients = array_diff($recipients, [$post->getUser()->getEmail()]);

        // Only notify for replies
        //if (null === $comment->getParent()) {
            //return;
        //}

        // Determine the users needing to be notified
        // This code notifies all people who replied to the same message
        //$notifiedUsers = new \SplObjectStorage();
        //$author = null;

        //if ($comment instanceof SignedCommentInterface) {

            //$notifiedUsers->attach($comment->getAuthor());
       // }

        //$parent = $comment->getParent();

        /*
        if ($parent instanceof SignedCommentInterface && $parent->getAuthor() !== $author && $parent->getAuthor() !== null) {
            $notifiedUsers->attach($parent->getAuthor());
        }

        $replies = $this->commentManager->findCommentTreeByCommentId($parent->getId());

        foreach ($replies as $replyTree) {
            $reply = $replyTree['comment'];

            if ($reply instanceof SignedCommentInterface && $reply->getAuthor() !== $author && $reply->getAuthor() !== null) {
                $notifiedUsers->attach($reply->getAuthor());
            }
        }
        */

        // Send the notifications
        $this->notificationManager->send($comment, $recipients, $post);
    }

    /**
     * @param string $owner
     * @return string
     */
    private function getPostClass(string $owner): string
    {
        switch ($owner) {
            case 'mot':
                return Mot::class;
            case 'locution':
                return Locution::class;
            case 'proverbe':
                return Proverbe::class;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown owner %s', $owner));
        }
    }

}