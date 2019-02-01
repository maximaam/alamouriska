<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CrudController
 * @package App\Controller
 */
class CrudController extends AbstractController
{
    /**
     * @Route("/ajouter/{item}", name="crud_create")
     */
    public function create()
    {
        return $this->render('crud/index.html.twig', [
            'controller_name' => 'CrudController',
        ]);
    }
}
