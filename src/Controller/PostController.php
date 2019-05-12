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
use App\Repository\CitationRepository;
use App\Repository\LocutionRepository;
use App\Repository\MotRepository;
use App\Repository\ProverbeRepository;
use App\Utils\LikingUtils;
use App\Utils\Linguistic;
use App\Utils\ModelUtils;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
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
    /**
     * @Route(
     *     "/{domain}",
     *     name="post_index",
     *     methods={"GET","POST"},
     *     requirements={"domain"="mots|locutions|proverbes|citations"}
     *     )
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $domain = $request->get('domain');
        $entity = ModelUtils::getEntityByDomain($domain);
        $class = 'App\\Entity\\' . $entity;

        /** @var  Mot|Locution|Proverbe|Citation $model */
        $model = (new $class())->setUser($this->getUser());

        $form = $this->createForm('App\\Form\\' . $entity . 'Type', $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->submitForm($form, $model, $domain, $request->getClientIp());
        }

        return $this->render('post/index.html.twig', [
            'domain' => $domain,
            'entity' => $entity,
            'form'  => $form->createView(),
            'posts'  => $this->getPaginator($paginator, \get_class($model), $request->query->getInt('page', 1), $request->query->getBoolean('enigmatique', false)),
            'likings' => $this->getLikings($entity)
        ]);
    }




    /**
     * @Route(
     *     "/{domain}/{id}/{slug}",
     *     name="post_show",
     *     methods={"GET"},
     *     requirements={"domain"="mots|locutions|proverbes|citations"}
     *     )
     *
     * @param Request $request
     * @param ThreadManagerInterface $threadManager
     * @param CommentManagerInterface $commentManager
     * @return Response
     * @throws \ReflectionException
     */
    public function show(CommentManagerInterface $commentManager, ThreadManagerInterface $threadManager, Request $request): Response
    {
        $domain = $request->get('domain');
        $entity = ModelUtils::getEntityByDomain($domain);

        /** @var  Mot|Locution|Proverbe|Citation $model */
        $model = $this->getDoctrine()->getManager()->find('App\\Entity\\' . $entity, $request->get('id'));

        $thread = $this->createThread($threadManager, $request, $model);
        $comments = $commentManager->findCommentTreeByThread($thread);

        return $this->render('post/show.html.twig', [
            'domain'    => $domain,
            'entity'    => $entity,
            'post'      => $model,
            'comments'  => $comments,
            'thread'    => $thread,
            'likings' => $this->getLikings($entity)
        ]);
    }

    /**
     * @Route("/supprimer/{domain}/{id}", name="post_delete", methods={"GET"})
     *
     * @param string $domain
     * @param string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function delete(string $domain, string $id): RedirectResponse
    {
        $entity = ModelUtils::getEntityByDomain($domain);

        /** @var  Mot|Locution|Proverbe|Citation $model */
        $model = $this->getDoctrine()->getManager()->find('App\\Entity\\' . $entity, $id);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (null !== $model) {

            if ($model->getUser() !== $this->getUser()) {
                throw new \Exception('Error.');
            }

            $manager = $this->getDoctrine()->getManager();

            if ($model instanceof Mot) {
                $motDeleted = (new MotDeleted())
                    ->setInLatin($model->getInLatin())
                    ->setInArabic($model->getInArabic())
                    ->setInTamazight($model->getInTamazight())
                    ->setDescription($model->getDescription())
                    ->setUserId($model->getUser()->getId())
                    ->setCreatedAt($model->getCreatedAt())
                ;

                $manager->persist($motDeleted);
            }

            $manager->remove($model);
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
                case 'mots':
                    return $this->render('post/search.html.twig', ['mots' => $motRepository->search($term)]);
                    break;

                case 'locutions':
                    return $this->render('post/search.html.twig', ['locutions' => $locutionRepository->search($term)]);
                    break;

                case 'proverbes':
                    return $this->render('post/search.html.twig', ['proverbes' => $proverbeRepository->search($term)]);
                    break;

                case 'citations':
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
     * @param Mot|Locution|Proverbe|Citation $model
     * @param string $domain
     * @param string $addr
     * @return RedirectResponse
     */
    private function submitForm(FormInterface $form, $model, string $domain, string $addr): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $model->setSlug(Linguistic::toSlug($model));
        $model->setAddr($addr);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($model);
        $entityManager->flush();
        unset($form, $model);

        return $this->redirectToRoute('post_index', ['domain' => $domain]);
    }

    /**
     * @param PaginatorInterface $paginator
     * @param string $class
     * @param int $page
     * @return PaginationInterface
     */
    private function getPaginator(PaginatorInterface $paginator, string $class, int $page, bool $question = null): PaginationInterface
    {
        /** @var QueryBuilder $query */
        $query = $this->getDoctrine()->getRepository($class)
            ->createQueryBuilder('q');

        if ($question) {
            $query
                ->andWhere('q.question = :question')
                ->setParameter('question', $question)
            ;
        }

        $query
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
            $thread->setOwner($owner->getShortName());
            $thread->setOwnerId($post->getId());

            // Add the thread
            $threadManager->saveThread($thread);
        }

        return $thread;
    }

}
