<?php

namespace App\EventListener;

use App\Entity\Joke;
use App\Entity\Comment;
use App\Entity\Expression;
use App\Entity\Word;
use App\Entity\Proverb;
use App\Entity\Thread;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CommentBundle\Event\CommentEvent;
use FOS\CommentBundle\Events;
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
            //FOSCommentEvents::COMMENT_POST_PERSIST => 'onCommentPostPersist',
            Events::COMMENT_PRE_PERSIST => 'onCommentPersist'
        ];
    }

    /**
     * @param CommentEvent $event
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function onCommentPersist(CommentEvent $event)
    {
        /** @var Comment $comment */
        $comment = $event->getComment();

        /** @var Thread $thread */
        $thread = $comment->getThread();
        $postOwner = $thread->getPost();



        //Only add comment should trigger
        /*
        if ($comment->getState() !== Comment::STATE_VISIBLE) {
            return;
        }
        */

        //Only new comments should trigger
        if (!$this->commentManager->isNewComment($comment)) {

            //If deleted, decrement comments number
            if ((int)$comment->getState() === Comment::STATE_DELETED) {
                $thread->decrementNumComments(1);
            } else {
                return;
            }
        }

        //file_put_contents(__FILE__ . '.html', serialize($comment));

        return;


        /** @var  Comment[] $comment */
        $comments = $this->commentManager->findCommentsByThread($thread);

        $recipients = [];

        foreach ($comments as $comment) {
            $email = $comment->getAuthor()->getEmail();

            if (!in_array($email, $recipients, true)) {
                \array_push($recipients, $email);
            }
        }

        /** @var Word|Expression|Proverb|Joke $post */
        $post = $this->em->getRepository('App\\Entity\\' . $owner)->find($thread->getPostId());

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
        $this->notificationManager->send($recipients, $post, $thread->getPermalink());
    }

}