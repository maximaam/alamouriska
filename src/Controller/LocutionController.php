<?php

namespace App\Controller;

use App\Entity\Locution;
use App\Form\Locution1Type;
use App\Repository\LocutionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/locution")
 */
class LocutionController extends AbstractController
{
    /**
     * @Route("/", name="locution_index", methods={"GET"})
     */
    public function index(LocutionRepository $locutionRepository): Response
    {
        return $this->render('locution/index.html.twig', [
            'locutions' => $locutionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="locution_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $locution = new Locution();
        $form = $this->createForm(Locution1Type::class, $locution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($locution);
            $entityManager->flush();

            return $this->redirectToRoute('locution_index');
        }

        return $this->render('locution/new.html.twig', [
            'locution' => $locution,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="locution_show", methods={"GET"})
     */
    public function show(Locution $locution): Response
    {
        return $this->render('locution/show.html.twig', [
            'locution' => $locution,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="locution_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Locution $locution): Response
    {
        $form = $this->createForm(Locution1Type::class, $locution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('locution_index', [
                'id' => $locution->getId(),
            ]);
        }

        return $this->render('locution/edit.html.twig', [
            'locution' => $locution,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="locution_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Locution $locution): Response
    {
        if ($this->isCsrfTokenValid('delete'.$locution->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($locution);
            $entityManager->flush();
        }

        return $this->redirectToRoute('locution_index');
    }
}
