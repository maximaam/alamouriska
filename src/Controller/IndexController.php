<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Entity\Page;
use App\Entity\Rating;
use App\Form\JournalType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('index/index.html.twig', [
            'journalForm' => $this->createForm(JournalType::class, new Journal())->createView(),
            'journals' => $this->getDoctrine()->getRepository(Journal::class)->findBy([], ['id' => 'DESC'], 20),
            'most_commented'=> [],
            'ratings' => $this->getDoctrine()->getRepository(Rating::class)->findAll(),
            //'has_rated' => null !== $this->getDoctrine()->getRepository(Rating::class)->findOneBy(['addr' => $request->getClientIp()]),
            'has_rated' => false,
            'page' => $this->getDoctrine()->getRepository(Page::class)->findOneBy(['alias' => 'homepage']),
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
