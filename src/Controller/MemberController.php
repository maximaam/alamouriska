<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MemberController
 * @Route("/membre")
 *
 * @package App\Controller
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/{username}", name="member_show")
     */
    public function show()
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }
}
