<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\User;
use App\Repository\JournalRepository;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
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
 * @Route("/async")
 *
 * @package App\Controller
 */
class AsyncController extends AbstractController
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    /**
     * @Route("/liking", name="async_liking")
     *
     * @param Request $request
     * @param LikingRepository $repository
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function liking(Request $request, LikingRepository $repository, TranslatorInterface $translator): JsonResponse
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (false === $request->isXmlHttpRequest()) {
            return new JsonResponse([], 403);
        }

        $user = $this->getUser();

        if ($user) {
            $entityManager = $this->getDoctrine()->getManager();

            $liking = $repository->findOneBy([
                'user'      => $user,
                'owner'     => $request->get('owner'),
                'ownerId'   => $request->get('ownerId')
                ]);

            //var_dump($liking); die;

            if (null === $liking) {
                $newLiking = (new Liking())
                    ->setUser($user)
                    ->setOwner($request->get('owner'))
                    ->setOwnerId($request->get('ownerId'));

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
     * @Route("/rating", name="async_rating")
     *
     * @param Request $request
     * @param RatingRepository $repository
     * @return JsonResponse
     * @throws \Exception
     */
    public function rating(Request $request, RatingRepository $repository): JsonResponse
    {
        if (false === $request->isXmlHttpRequest()) {
            return new JsonResponse([], 403);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $rating = $repository->findOneBy([
            'addr'     => $request->getClientIp(),
            //'createdAt' => $request->get('ownerId')
        ]);

        if (null === $rating) {
            $newRating = (new Rating())
                ->setRating($request->get('rating'))
                ->setAddr($request->getClientIp())
            ;

            $entityManager->persist($newRating);

            $entityManager->flush();

            return new JsonResponse([
                'status' => self::STATUS_SUCCESS,
            ], 200);
        }

        return new JsonResponse(['status' => self::STATUS_ERROR], 410);
    }


    /**
     * @Route("/del-journal", name="async_del_journal")
     *
     * @param Request $request
     * @param JournalRepository $repository
     * @return JsonResponse
     */
    public function delJournal(Request $request, JournalRepository $repository): JsonResponse
    {
        if (false === $request->isXmlHttpRequest()) {
            return new JsonResponse([], 403);
        }

        $user = $this->getUser();

        if ($user) {
            $journal = $repository->findOneBy(['user' => $user, 'id' => $request->get('id')]);

            if (null !== $journal) {
                $entityManager = $this->getDoctrine()->getManager();
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
     * @Route("/ask-log-in", name="async_ask_log_in")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function askLogIn(Request $request)
    {
        if (false === $request->isXmlHttpRequest()) {
            return new JsonResponse('Error', 403);
        }

        return $this->render('partials/modal.html.twig');
    }

    /**
     * @Route("/member-contact", name="async_member_contact")
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function memberContact(Request $request, \Swift_Mailer $mailer): Response
    {
        if (false === $request->isXmlHttpRequest()) {
            return new JsonResponse('Error', 403);
        }

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

}
