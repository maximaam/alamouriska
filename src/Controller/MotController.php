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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\CommentBundle\Model\Thread;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;

/**
 * @Route("/mot")
 */
class MotController extends AbstractController
{
    /**
     * @Route("/", name="mot_index", methods={"GET","POST"})
     *
     * @param MotRepository $motRepository
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(MotRepository $motRepository, Request $request): Response
    {
        $mot = new Mot();
        $mot->setUser($this->getUser());

        $form = $this->createForm(MotType::class, $mot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mot);
            $entityManager->flush();

            return $this->redirectToRoute('mot_index');
        }

        return $this->render('mot/index.html.twig', [
            'mot'   => $mot,
            'mots'  => $motRepository->findBy([], ['createdAt' => 'DESC']),
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="mot_new", methods={"GET","POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, ThreadManagerInterface $threadManager): Response
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
     *
     * @param Mot $mot
     * @param Request $request
     * @param ThreadManagerInterface $threadManager
     * @param CommentManagerInterface $commentManager
     * @return Response
     * @throws \ReflectionException
     */
    public function show(Mot $mot, Request $request, ThreadManagerInterface $threadManager, CommentManagerInterface $commentManager): Response
    {
        //Use a hash of the entity and its ID as the thread id to enable multilple entities having same IDs to get comments
        $threadIdentifier = \md5(\get_class($mot) . $mot->getId());

        /** @var ThreadManagerInterface $thread */
        $thread = $threadManager->findThreadById($threadIdentifier);

        if (null === $thread) {
            $owner = new \ReflectionClass($mot);

            /** @var \App\Entity\Thread $thread */
            $thread = $threadManager->createThread();
            $thread->setId($threadIdentifier);
            $thread->setPermalink($request->getUri());
            $thread->setOwner(\strtolower($owner->getShortName()));
            $thread->setOwnerId($mot->getId());

            // Add the thread
            $threadManager->saveThread($thread);
        }

        $comments = $commentManager->findCommentTreeByThread($thread);

        return $this->render('mot/show.html.twig', [
            'mot' => $mot,
            'comments' => $comments,
            'thread' => $thread,
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
