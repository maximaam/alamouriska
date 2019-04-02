<?php

namespace App\Controller;

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
     * @return Response
     * @throws \Exception
     */
    public function liking(Request $request, LikingRepository $repository, TranslatorInterface $translator)
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




}
