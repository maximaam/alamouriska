<?php

namespace App\Controller;

use App\Entity\Proverbe;
use App\Form\Proverbe1Type;
use App\Repository\ProverbeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/proverbe")
 */
class ProverbeController extends AbstractController
{
    /**
     * @Route("/", name="proverbe_index", methods={"GET"})
     */
    public function index(ProverbeRepository $proverbeRepository): Response
    {
        return $this->render('proverbe/index.html.twig', [
            'proverbes' => $proverbeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="proverbe_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $proverbe = new Proverbe();
        $form = $this->createForm(Proverbe1Type::class, $proverbe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($proverbe);
            $entityManager->flush();

            return $this->redirectToRoute('proverbe_index');
        }

        return $this->render('proverbe/new.html.twig', [
            'proverbe' => $proverbe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="proverbe_show", methods={"GET"})
     */
    public function show(Proverbe $proverbe): Response
    {
        return $this->render('proverbe/show.html.twig', [
            'proverbe' => $proverbe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="proverbe_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Proverbe $proverbe): Response
    {
        $form = $this->createForm(Proverbe1Type::class, $proverbe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('proverbe_index', [
                'id' => $proverbe->getId(),
            ]);
        }

        return $this->render('proverbe/edit.html.twig', [
            'proverbe' => $proverbe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="proverbe_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Proverbe $proverbe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$proverbe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($proverbe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('proverbe_index');
    }
}
