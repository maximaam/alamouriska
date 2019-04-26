<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Controller;

use App\Entity\Liking;
use App\Entity\Locution;
use App\Entity\Mot;
use App\Entity\MotDeleted;
use App\Entity\Proverbe;
use App\Entity\Thread;
use App\Form\LocutionType;
use App\Form\MotType;
use App\Repository\LocutionRepository;
use App\Repository\ProverbeRepository;
use App\Utils\LikingUtils;
use App\Utils\Linguistic;
use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PostsController
 * @package App\Controller
 */
class PostsController extends AbstractController
{
    /**
     * @Route("/mots", name="mots_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function motsIndex(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $mot = (new Mot())->setUser($this->getUser());

        $form = $this->createForm(MotType::class, $mot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $mot->setSlug(Linguistic::toSlug($mot->getInLatin()));

            $entityManager->persist($mot);
            $entityManager->flush();

            return $this->redirectToRoute('mot_index');
        }

        $likings = $this->getDoctrine()->getRepository(Liking::class)
            ->findBy(['owner' => 'mot']);

        $motsQuery = $this->getDoctrine()->getRepository(Mot::class)
            ->createQueryBuilder('m')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        $mots = $paginator->paginate($motsQuery, $request->query->getInt('page', 1), 3);

        return $this->render('posts/mot_index.html.twig', [
            'mot'   => $mot,
            'mots'  => $mots,
            'form'  => $form->createView(),
            'likings' => LikingUtils::getLikingsUsersIds($likings)
        ]);
    }

    /**
     * @Route("/locutions", name="locutions_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function locutionsIndex(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $locution = (new Locution())->setUser($this->getUser());

        $form = $this->createForm(LocutionType::class, $locution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $locution->setSlug(Linguistic::toSlug($locution->getLocution()));

            $entityManager->persist($locution);
            $entityManager->flush();

            return $this->redirectToRoute('mot_index');
        }

        $likings = $this->getDoctrine()->getRepository(Liking::class)
            ->findBy(['owner' => 'locution']);

        $motsQuery = $this->getDoctrine()->getRepository(Locution::class)
            ->createQueryBuilder('m')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        $locutions = $paginator->paginate($motsQuery, $request->query->getInt('page', 1), 3);

        return $this->render('posts/mot_index.html.twig', [
            'locution'   => $locution,
            'locutions'  => $locutions,
            'form'  => $form->createView(),
            'likings' => LikingUtils::getLikingsUsersIds($likings)
        ]);
    }

    /**
     * @Route("/locutions", name="locutions_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function proverbesIndex(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $locution = (new Locution())->setUser($this->getUser());

        $form = $this->createForm(LocutionType::class, $locution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $locution->setSlug(Linguistic::toSlug($locution->getLocution()));

            $entityManager->persist($locution);
            $entityManager->flush();

            return $this->redirectToRoute('mot_index');
        }

        $likings = $this->getDoctrine()->getRepository(Liking::class)
            ->findBy(['owner' => 'locution']);

        $motsQuery = $this->getDoctrine()->getRepository(Locution::class)
            ->createQueryBuilder('m')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        $locutions = $paginator->paginate($motsQuery, $request->query->getInt('page', 1), 3);

        return $this->render('posts/mot_index.html.twig', [
            'locution'   => $locution,
            'locutions'  => $locutions,
            'form'  => $form->createView(),
            'likings' => LikingUtils::getLikingsUsersIds($likings)
        ]);
    }

    /**
     * @Route("/mot/{id}/{slug}", name="mot_show", methods={"GET"})
     *
     * @param Mot $mot
     * @param Request $request
     * @param ThreadManagerInterface $threadManager
     * @param CommentManagerInterface $commentManager
     * @return Response
     * @throws \ReflectionException
     */
    public function motShow(Mot $mot, CommentManagerInterface $commentManager, ThreadManagerInterface $threadManager, Request $request): Response
    {
        $thread = $this->createThread($threadManager, $request, $mot);
        $comments = $commentManager->findCommentTreeByThread($thread);

        return $this->render('posts/mot_show.html.twig', [
            'mot' => $mot,
            'comments' => $comments,
            'thread' => $thread,
        ]);
    }

    /**
     * @Route("/mot/supprimer/{id}", name="mot_delete", methods={"GET"})
     *
     * @param Mot $mot
     * @return RedirectResponse
     * @throws \Exception
     */
    public function motDelete(Mot $mot): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($mot) {

            if ($mot->getUser() !== $this->getUser()) {
                throw new \Exception('Error.');
            }

            $motDeleted = (new MotDeleted())
                ->setInLatin($mot->getInLatin())
                ->setInArabic($mot->getInArabic())
                ->setInTamazight($mot->getInTamazight())
                ->setDescription($mot->getDescription())
                ->setUserId($mot->getUser()->getId())
                ->setCreatedAt($mot->getCreatedAt())
            ;

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($motDeleted);
            $manager->remove($mot);
            $manager->flush();

            return $this->redirectToRoute('user_show', ['username' => $this->getUser()->getUsername()]);
        }

        throw new \Exception('Cette publication est inconnue.');
    }

    /**
     * @param ThreadManagerInterface $threadManager
     * @param Request $request
     * @param Mot|Locution|Proverbe $post
     * @return ThreadInterface
     * @throws \ReflectionException
     */
    private function createThread(ThreadManagerInterface $threadManager, Request $request, $post): ThreadInterface
    {
        //Use a hash of the entity and its ID as the thread id to enable multiple entities having same IDs to get comments
        $threadIdentifier = \md5(\get_class($post) . $post->getId());

        /** @var ThreadManagerInterface $thread */
        $thread = $threadManager->findThreadById($threadIdentifier);

        if (null === $thread) {
            $owner = new \ReflectionClass($post);

            /** @var Thread $thread */
            $thread = $threadManager->createThread();
            $thread->setId($threadIdentifier);
            $thread->setPermalink($request->getUri());
            $thread->setOwner(\strtolower($owner->getShortName()));
            $thread->setOwnerId($post->getId());

            // Add the thread
            $threadManager->saveThread($thread);
        }

        return $thread;
    }

}
