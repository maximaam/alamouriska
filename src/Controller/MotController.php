<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Controller;

use App\Entity\Mot;
use App\Form\MotType;
use App\Repository\MotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mot")
 */
class MotController extends AbstractController
{
    /**
     * @Route("/", name="mot_index", methods={"GET","POST"})
     *
     * @param MotRepository $motRepository
     * @return Response
     */
    public function index(MotRepository $motRepository, Request $request): Response
    {

        $mot = new Mot();
        $mot->setUser($this->getUser());

        $form = $this->createForm(MotType::class, $mot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mot);
            $entityManager->flush();

            return $this->redirectToRoute('mot_index');
        }

        return $this->render('mot/index.html.twig', [
            'mot' => $mot,
            'form' => $form->createView(),
            'mots' => $motRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="mot_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request): Response
    {
        $mot = new Mot();
        $mot->setUser($this->getUser());

        $form = $this->createForm(MotType::class, $mot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mot);
            $entityManager->flush();

            return $this->redirectToRoute('mot_index');
        }

        return $this->render('mot/new.html.twig', [
            'mot' => $mot,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mot_show", methods={"GET"})
     */
    public function show(Mot $mot): Response
    {
        return $this->render('mot/show.html.twig', [
            'mot' => $mot,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="mot_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Mot $mot): Response
    {
        $form = $this->createForm(MotType::class, $mot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mot_index', [
                'id' => $mot->getId(),
            ]);
        }

        return $this->render('mot/edit.html.twig', [
            'mot' => $mot,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mot_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Mot $mot): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mot->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mot_index');
    }
}
