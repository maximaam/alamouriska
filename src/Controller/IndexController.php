<?php

namespace App\Controller;

use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index_index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'page'   => $this->getDoctrine()->getRepository(Page::class)->findOneBy(['alias' => 'homepage']),
        ]);
    }

    /**
     * @Route("/page/{alias}", name="index_page")
     *
     * @param Page $page
     * @return Response
     */
    public function page(Page $page): Response
    {
        return $this->render(sprintf('index/%s.html.twig', $page->getAlias()), [
            'page'   => $page,
        ]);
    }
}
