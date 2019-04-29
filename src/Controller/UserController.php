<?php

namespace App\Controller;

use App\Entity\Citation;
use App\Entity\Locution;
use App\Entity\Mot;
use App\Entity\Proverbe;
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
     * @Route("/{username}", name="user_show")
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/profile.html.twig', ['user' => $user]);
    }
}
