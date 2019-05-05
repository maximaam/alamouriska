<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Controller;

use App\Entity\Citation;
use App\Entity\Liking;
use App\Entity\Locution;
use App\Entity\Mot;
use App\Entity\MotDeleted;
use App\Entity\Proverbe;
use App\Entity\Thread;
use App\Form\CitationType;
use App\Form\LocutionType;
use App\Form\MotType;
use App\Form\ProverbeType;
use App\Repository\CitationRepository;
use App\Repository\LocutionRepository;
use App\Repository\MotRepository;
use App\Repository\ProverbeRepository;
use App\Utils\LikingUtils;
use App\Utils\Linguistic;
use App\Utils\PhpUtils;
use Knp\Component\Pager\Pagination\PaginationInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{ RedirectResponse, Request, Response };
use Symfony\Component\Routing\Annotation\Route;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PostController
 * @package App\Controller
 */
class PostController extends AbstractController
{



    public function __construct()
    {

    }


    /**
     * @Route(
     *     "/{almrsk}",
     *     name="post_index",
     *     methods={"GET","POST"},
     *     requirements={"almrsk=mots|locutions|proverbes|citations"}
     *     )
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $almrsk = $request->get('almrsk');

        return call_user_func_array([$this, $almrsk], [Request $request, PaginatorInterface $paginator])
    }

    private function mots(Request $request, PaginatorInterface $paginator)
    {
        $mot = (new Mot())->setUser($this->getUser());

        $form = $this->createForm(MotType::class, $mot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->submitForm($form, $mot, $request->getClientIp());
        }

        return $this->render('post/mots_index.html.twig', [
            'form'  => $form->createView(),
            'mots'  => $this->getPaginator($paginator, Mot::class, $request->query->getInt('page', 1)),
            'likings' => $this->getLikings('mot')
        ]);
    }

    /**
     * @Route("/locutions", name="post_locution_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \ReflectionException
     */
    public function locutionsIndex(Request $request, PaginatorInterface $paginator): Response
    {
        $locution = (new Locution())->setUser($this->getUser());

        $form = $this->createForm(LocutionType::class, $locution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->submitForm($form, $locution, $request->getClientIp());
        }

        return $this->render('post/locutions_index.html.twig', [
            'form'  => $form->createView(),
            'locutions'  => $this->getPaginator($paginator, Locution::class, $request->query->getInt('page', 1)),
            'likings' => $this->getLikings('locution')
        ]);
    }

    /**
     * @Route("/proverbes", name="post_proverbe_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \ReflectionException
     */
    public function proverbesIndex(Request $request, PaginatorInterface $paginator): Response
    {
        $proverbe = (new Proverbe())->setUser($this->getUser());

        $form = $this->createForm(ProverbeType::class, $proverbe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->submitForm($form, $proverbe, $request->getClientIp());
        }

        return $this->render('post/proverbes_index.html.twig', [
            'form'  => $form->createView(),
            'proverbes'  => $this->getPaginator($paginator, Proverbe::class, $request->query->getInt('page', 1)),
            'likings' => $this->getLikings('proverbe')
        ]);
    }

    /**
     * @Route("/citations", name="post_citation_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \ReflectionException
     */
    public function citationsIndex(Request $request, PaginatorInterface $paginator): Response
    {
        $citation = (new Citation())->setUser($this->getUser());

        $form = $this->createForm(CitationType::class, $citation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->submitForm($form, $citation, $request->getClientIp());
        }

        return $this->render('post/proverbes_index.html.twig', [
            'form'  => $form->createView(),
            'citations'  => $this->getPaginator($paginator, Citation::class, $request->query->getInt('page', 1)),
            'likings' => $this->getLikings('citation')
        ]);
    }

    /**
     * @Route("/mots/{id}/{slug}", name="post_mot_show", methods={"GET"})
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

        return $this->render('post/mot_show.html.twig', [
            'mot' => $mot,
            'comments' => $comments,
            'thread' => $thread,
        ]);
    }

    /**
     * @Route("/locution/{id}/{slug}", name="post_locution_show", methods={"GET"})
     *
     * @param Locution $locution
     * @param Request $request
     * @param ThreadManagerInterface $threadManager
     * @param CommentManagerInterface $commentManager
     * @return Response
     * @throws \ReflectionException
     */
    public function locutionShow(Locution $locution, CommentManagerInterface $commentManager, ThreadManagerInterface $threadManager, Request $request): Response
    {
        $thread = $this->createThread($threadManager, $request, $locution);
        $comments = $commentManager->findCommentTreeByThread($thread);

        return $this->render('post/locution_show.html.twig', [
            'locution' => $locution,
            'comments' => $comments,
            'thread' => $thread,
        ]);
    }

    /**
     * @Route("/mot/supprimer/{id}", name="post_mot_delete", methods={"GET"})
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
     * @Route("/recherche", name="post_search", methods={"GET"})
     *
     * @param Request $request
     * @param MotRepository $motRepository
     * @param LocutionRepository $locutionRepository
     * @param ProverbeRepository $proverbeRepository
     * @param CitationRepository $citationRepository
     * @return Response
     * @throws \Exception
     */
    public function search(Request $request, MotRepository $motRepository, LocutionRepository $locutionRepository, ProverbeRepository $proverbeRepository, CitationRepository $citationRepository): Response
    {
        $domain = $request->get('domaine');
        $term = $request->get('terme');

        if (\strlen($domain) >= 3 && \strlen($term) >= 3) {
            switch ($domain) {
                case 'mot':
                    return $this->render('post/search.html.twig', ['mots' => $motRepository->search($term)]);
                    break;

                case 'locution':
                    return $this->render('post/search.html.twig', ['locutions' => $locutionRepository->search($term)]);
                    break;

                case 'proverbe':
                    return $this->render('post/search.html.twig', ['proverbes' => $proverbeRepository->search($term)]);
                    break;

                case 'citation':
                    return $this->render('post/search.html.twig', ['citations' => $citationRepository->search($term)]);
                    break;

                case 'tout':
                    return $this->render('post/search.html.twig', [
                        'mots'      => $motRepository->search($term),
                        'locutions' => $locutionRepository->search($term),
                        'proverbes' => $proverbeRepository->search($term),
                        'citations' => $citationRepository->search($term),
                    ]);
                    break;
            }
        }

        throw new \Exception('Cette publication est inconnue.');
    }

    /**
     * @param FormInterface $form
     * @param Mot|Locution|Proverbe|Citation $post
     * @param string $addr
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    private function submitForm(FormInterface $form, $post, string $addr): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $post->setSlug(Linguistic::toSlug($post));
        $post->setAddr($addr);
        $route = 'post_' . \strtolower(PhpUtils::getClassName($post)) . '_index';

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();
        unset($form, $post);

        return $this->redirectToRoute($route);
    }

    /**
     * @param PaginatorInterface $paginator
     * @param string $class
     * @param int $page
     * @return PaginationInterface
     */
    private function getPaginator(PaginatorInterface $paginator, string $class, int $page): PaginationInterface
    {
        $query = $this->getDoctrine()->getRepository($class)
            ->createQueryBuilder('q')
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery();

        return $paginator->paginate($query, $page, 3);
    }

    /**
     * @param string $owner
     * @return array
     */
    private function getLikings(string $owner): array
    {
        $likings = $this->getDoctrine()->getRepository(Liking::class)
            ->findBy(['owner' => $owner]);

        return LikingUtils::getLikingsUsersIds($likings);
    }

    /**
     * @param ThreadManagerInterface $threadManager
     * @param Request $request
     * @param Mot|Locution|Proverbe|Citation $post
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
