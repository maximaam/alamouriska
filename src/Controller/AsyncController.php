<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Expression;
use App\Entity\Joke;
use App\Entity\Journal;
use App\Entity\Proverb;
use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Word;
use App\Form\CommentType;
use App\Form\JournalType;
use App\Repository\JournalRepository;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
use App\Utils\ModelUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Liking;
use App\Repository\LikingRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @return Response
     */
    public function memberContact(Request $request, \Swift_Mailer $mailer): Response
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $sender = $userRepo->find($request->get('sender'));
        $receiver = $userRepo->find($request->get('receiver'));

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
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function commentCreate(Request $request, \Swift_Mailer $mailer): Response
    {
        list($domain, $id) = \explode('-', $request->request->get('comment')['type']);
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
     * @Route("/comment-remove", name="async_comment_remove", condition="request.isXmlHttpRequest()")
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

}
