<?php

namespace App\Controller;

use App\Entity\ThumbUp;
use App\Repository\ThumbUpRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/like", name="async_like")
     *
     * @param Request $request
     * @param ThumbUpRepository $repository
     * @return Response
     * @throws \Exception
     */
    public function like(Request $request, ThumbUpRepository $repository)
    {
        $user = $this->getUser();

        if ($user) {
            $entityManager = $this->getDoctrine()->getManager();

            $like = $repository->findOneBy([
                'user'      => $user,
                'owner'     => $request->get('owner'),
                'ownerId'   => $request->get('id')
                ]);

            if (null === $like) {
                $newLike = (new ThumbUp())
                    ->setUser($user)
                    ->setOwner($request->get('owner'))
                    ->setOwnerId($request->get('id'));

                $entityManager->persist($newLike);
                $entityManager->flush();

            } else {
                $entityManager->remove($like);
            }

            return new JsonResponse([
                'status'    => 'ok'
            ]);
        }

        return new JsonResponse([
            'status'    => 'error'
        ]);

    }


}
