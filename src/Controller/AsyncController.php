<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ThumbUp;
use App\Repository\ThumbUpRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AsyncController
 *
 * @Route("/async")
 *
 * @package App\Controller
 */
class AsyncController extends AbstractController
{
    /**
     * @Route("/thumbs-up", name="async_like")
     *
     * @param Request $request
     * @param ThumbUpRepository $repository
     * @return Response
     * @throws \Exception
     */
    public function thumbsUp(Request $request, ThumbUpRepository $repository)
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([], 403);
        }

        $user = $this->getUser();

        if ($user) {
            $entityManager = $this->getDoctrine()->getManager();

            $thumbsUp = $repository->findOneBy([
                'user'      => $user,
                'owner'     => $request->get('owner'),
                'ownerId'   => $request->get('id')
                ]);

            if (null === $thumbsUp) {
                $newThumbsUp = (new ThumbUp())
                    ->setUser($user)
                    ->setOwner($request->get('owner'))
                    ->setOwnerId($request->get('id'));

                $entityManager->persist($newThumbsUp);
                $entityManager->flush();

            } else {
                $entityManager->remove($thumbsUp);
            }

            return new JsonResponse([
                'status'    => 'ok'
            ], 200);
        }

        return new JsonResponse([
            'status'    => 'error'
        ], 410);

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
