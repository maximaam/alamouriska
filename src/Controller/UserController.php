<?php

namespace App\Controller;

use App\Entity\Mot;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MemberController
 * @Route("/membre")
 *
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{id}/{username}", name="user_show")
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'mots'  => $this->getDoctrine()->getRepository(Mot::class)->findBy(['user' => $user]),
            //'lo'  => $this->getDoctrine()->getRepository(Location::class)->findBy(['user' => $user]),
            //'mots'  => $this->getDoctrine()->getRepository(User::class)->findBy(['user' => $user]),
        ]);
    }
}
