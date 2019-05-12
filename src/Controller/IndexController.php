<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Entity\Locution;
use App\Entity\Mot;
use App\Entity\Page;
use App\Entity\Rating;
use App\Form\JournalType;
use App\Form\LocutionType;
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
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $journal = (new Journal())->setUser($this->getUser());

        $form = $this->createForm(JournalType::class, $journal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $journal->setAddr($request->getClientIp());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($journal);
            $entityManager->flush();

            return $this->redirectToRoute('index_index');
        }

        $journals = $this->getDoctrine()->getRepository(Journal::class)->findBy([], ['id' => 'DESC'], 20);

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
            'journals' => $journals,
            'ratings' => $this->getDoctrine()->getRepository(Rating::class)->findAll(),
            'has_rated' => null !== $this->getDoctrine()->getRepository(Rating::class)->findOneBy(['addr' => $request->getClientIp()]),
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
