<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentMailQueue;
use App\Entity\Expression;
use App\Entity\Joke;
use App\Entity\Journal;
use App\Entity\Proverb;
use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Word;
use App\Form\CommentType;
use App\Form\JournalType;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
use App\Utils\ModelUtils;
use App\Utils\PhpUtils;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Liking;
use App\Repository\LikingRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class AsyncController
 *
 * @Route("/async", name="async_", condition="request.isXmlHttpRequest()")
 *
 * @package App\Controller
 */
class AsyncController extends AbstractController
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    /**
     * @Route("/liking", name="liking")
     *
     * @param Request $request
     * @param LikingRepository $repository
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function liking(Request $request, LikingRepository $repository, TranslatorInterface $translator): JsonResponse
    {
        if (null !== $user = $this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();
            list($owner, $ownerId) = \explode('-', $request->get('owner'));

            $liking = $repository->findOneBy(['user' => $user, 'owner' => $owner, 'ownerId' => $ownerId]);

            if (null === $liking) {
                $newLiking = (new Liking())
                    ->setUser($user)
                    ->setOwner($owner)
                    ->setOwnerId($ownerId);

                $entityManager->persist($newLiking);
                $action = 1;
                $actionLabel = $translator->trans('label.liking_remove');
            } else {
                $entityManager->remove($liking);
                $action = 2;
                $actionLabel = $translator->trans('label.liking_add');
            }

            $entityManager->flush();

            return new JsonResponse([
                'status' => self::STATUS_SUCCESS,
                'action' => $action,
                'actionLabel' => $actionLabel,
            ], 200);
        }

        return new JsonResponse(['status' => self::STATUS_ERROR], 410);
    }

    /**
     * @Route("/rating", name="rating")
     *
     * @param Request $request
     * @param RatingRepository $repository
     * @return JsonResponse
     * @throws \Exception
     */
    public function rating(Request $request, RatingRepository $repository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        $rating = $repository->findOneBy([
            'addr'     => $request->getClientIp(),
            //'createdAt' => $request->get('ownerId')
        ]);

        //if (null === $rating) {
            $newRating = (new Rating())
                ->setRating($request->get('rating'))
                ->setAddr($request->getClientIp())
            ;

            $entityManager->persist($newRating);

            $entityManager->flush();

            return new JsonResponse([
                'status' => self::STATUS_SUCCESS,
            ], 200);
        //}

        return new JsonResponse(['status' => self::STATUS_ERROR], 410);
    }

    /**
     * @Route("/journal-create", name="journal_create")
     *
     * @param Request $request
     * @return Response
     */
    public function journalCreate(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $journal = (new Journal())->setUser($this->getUser());
        $form = $this
            ->createForm(JournalType::class, $journal)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $journal->setAddr($request->getClientIp());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($journal);
            $entityManager->flush();

            return $this->render('partials/_comment-item.html.twig', [
                'object'   => $journal,
                'object_type' => 'journal'
            ]);
        }

        return new Response(self::STATUS_ERROR);
    }


    /**
     * @Route("/journal-remove", name="journal_remove")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function journalRemove(Request $request): JsonResponse
    {
        if (null !== $user = $this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var Journal $journal */
            $journal = $entityManager->find(Journal::class, (int)$request->get('uid'));

            if (null !== $journal && $user === $journal->getUser()) {
                $entityManager->remove($journal);
                $entityManager->flush();

                return new JsonResponse([
                    'status' => self::STATUS_SUCCESS,
                ], 200);
            }
        }

        return new JsonResponse(['status' => self::STATUS_ERROR], 410);
    }

    /**
     * @Route("/member-contact", name="member_contact")
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param UserRepository $userRepository
     * @return Response
     */
    public function memberContact(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository): Response
    {
        $sender = $userRepository->find($request->get('sender'));
        $receiver = $userRepository->find($request->get('receiver'));

        if ($sender && $receiver) {
            $message = (new \Swift_Message('Message privé de ' . $sender->getUsername()))
                ->setFrom('alamouriska.app@gmail.com', 'ALAMOURISKA')
                ->setTo($receiver->getEmail())
                ->setBody($this->render('emails/message__to-member.html.twig', [
                    'sender'  => $sender,
                    'receiver' => $receiver,
                    'message' => $request->get('message')]
                ), 'text/html');

            $mailer->send($message);

            return new Response('<i class="fa fa-thumbs-up"></i> Merci. <br>Ton message a été envoyé.');
        }

        return new Response('Erreur inconnue.');
    }

    /**
     * @Route("/comment-create", name="comment_create")
     *
     * @param Request $request
     * @return Response
     * @throws \ReflectionException
     */
    public function commentCreate(Request $request): Response
    {
        list($domain, $id) = \explode('_', $request->request->get('comment')['type']);
        $entity = ModelUtils::getEntityByDomain($domain);
        $manager = $this->getDoctrine()->getManager();

        /** @var Word|Expression|Proverb|Joke $post */
        if (null !== $post = $manager->find('App\\Entity\\' . $entity, $id)) {
            $comment = new Comment();
            $comment
                ->setUser($this->getUser())
                ->setPost($post);

            $form = $this
                ->createForm(CommentType::class, $comment)
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $postEntity = PhpUtils::getClassName($post);

                $mailQueue = $manager
                    ->getRepository(CommentMailQueue::class)
                    ->findOneBy(['post' => $postEntity, 'postId' => $post->getId()]);

                if (null === $mailQueue) {
                    $mailQueue = (new CommentMailQueue())
                        ->setPost($postEntity)
                        ->setPostId($post->getId());

                    $manager->persist($mailQueue);
                }

                $manager->persist($comment);
                $manager->flush();

                return $this->render('partials/_comment-item.html.twig', [
                    'object' => $comment,
                    'object_type' => 'comment'
                ]);
            }
        }

        return new Response(self::STATUS_ERROR);
    }

    /**
     * @Route("/comment-remove", name="comment_remove")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function commentRemove(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if ($user) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var Comment $comment */
            $comment = $entityManager->find(Comment::class, (int)$request->get('uid'));

            if (null !== $comment && $comment->getUser() === $user) {
                $entityManager->remove($comment);
                $entityManager->flush();

                return new JsonResponse([
                    'status' => self::STATUS_SUCCESS,
                ], 200);
            }
        }

        return new JsonResponse(['status' => self::STATUS_ERROR], 410);
    }

    /**
     * https://ourcodeworld.com/articles/read/459/how-to-authenticate-login-manually-an-user-in-a-controller-with-or-without-fosuserbundle-on-symfony-3
     *
     * @Route("/fb-login", name="fb_login")
     *
     * @param Request $request
     * @param TokenStorageInterface $storage
     * @param EventDispatcherInterface $eventDispatcher
     * @return JsonResponse
     * @throws \Exception
     */
    public function fbLogin(Request $request, TokenStorageInterface $storage, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $username = $request->get('name');
        $email = $request->get('email');
        $facebookId = $request->get('id');
        $response = [
            'register'  => 0,
            'status'    => 0
        ];

        if (null === $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['facebookId' => $facebookId])) {
            $user = $this->fbRegister($email, $username, $facebookId);
            $response['register'] = 1;
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $storage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $eventDispatcher->dispatch("security.interactive_login", $event);

        if (null !== $this->getUser()) {
            $response['status'] = 1;
        }

        return new JsonResponse($response);
    }

    /**
     * @param string $email
     * @param string $name
     * @param int $facebookId
     * @return UserInterface
     * @throws \Exception
     */
    protected function fbRegister(string $email, string $name, int $facebookId): UserInterface
    {

        $user = new User();
        $user
            ->setFacebookId($facebookId)
            ->setEmail($email)
            ->setEmailCanonical($email)
            ->setUsername($name)
            ->setUsernameCanonical($name)
            ->setEnabled(true)
            ->setPlainPassword($email)
            ->setRoles([]);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $user;
    }

}
