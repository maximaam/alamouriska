<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Controller;

use App\Entity\AbstractPost;
use App\Entity\Comment;
use App\Entity\Deleted;
use App\Entity\Joke;
use App\Entity\Liking;
use App\Entity\Expression;
use App\Entity\Word;
use App\Entity\Proverb;
use App\Entity\Thread;
use App\Form\CommentType;
use App\Repository\JokeRepository;
use App\Repository\ExpressionRepository;
use App\Repository\WordRepository;
use App\Repository\ProverbRepository;
use App\Utils\LikingUtils;
use App\Utils\Linguistic;
use App\Utils\ModelUtils;
use App\Utils\PhpUtils;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{ RedirectResponse, Request, Response };
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     *     requirements={"domain"="%seo_route_domains%"}
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

        /** @var  Word|Expression|Proverb|Joke $model */
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
     *     "/{domain}/{id<\d+>}/{slug}",
     *     name="post_show",
     *     methods={"GET"},
     *     requirements={"domain"="%seo_route_domains%"}
     *     )
     *
     * @param Request $request
     * @return Response
     * @throws \ReflectionException
     */
    public function show(Request $request): Response
    {
        $domain = $request->get('domain');
        $entity = ModelUtils::getEntityByDomain($domain);

        /** @var  Word|Expression|Proverb|Joke $model */
        $model = $this->getDoctrine()->getManager()->find('App\\Entity\\' . $entity, $request->get('id'));

        if (null === $model) {
            throw new NotFoundHttpException('Post introuvable');
        }

        return $this->render('post/show.html.twig', [
            'comment_form'    => $this->createForm(CommentType::class, new Comment())->createView(),
            'domain'    => $domain,
            'entity'    => $entity,
            'post'      => $model,
            'likings' => $this->getLikings($entity)
        ]);
    }

    /**
     * @Route(
     *     "/supprimer/{domain}/{id<\d+>}",
     *     name="post_delete",
     *     methods={"GET"},
     *     requirements={"domain"="%seo_route_domains%"}
     *     )
     *
     * @param string $domain
     * @param string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function delete(string $domain, string $id): RedirectResponse
    {
        $entity = ModelUtils::getEntityByDomain($domain);

        /** @var  Word|Expression|Proverb|Joke $model */
        $model = $this->getDoctrine()->getManager()->find('App\\Entity\\' . $entity, $id);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (null !== $model) {
            if ($model->getUser() !== $this->getUser()) {
                throw new \Exception('Error.');
            }

            $manager = $this->getDoctrine()->getManager();

            $deleted = (new Deleted())
                ->setCreatedAt($model->getCreatedAt())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setPost($model->getPost())
                ->setDescription($model->getDescription())
                ->setUserId($model->getUser()->getId())
                ->setUsername($model->getUser()->getUsername())
                ;

            $manager->persist($deleted);
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
     * @param WordRepository $wordRepository
     * @param ExpressionRepository $expressionRepository
     * @param ProverbRepository $proverbRepository
     * @param JokeRepository $jokeRepository
     * @return Response
     * @throws \Exception
     */
    public function search(Request $request, WordRepository $wordRepository, ExpressionRepository $expressionRepository, ProverbRepository $proverbRepository, JokeRepository $jokeRepository): Response
    {
        $domain = $request->get('domaine');
        $term = $request->get('terme');

        if (\strlen($domain) >= 3 && \strlen($term) >= 3) {
            switch ($domain) {
                case 'mots':
                    return $this->render('post/search.html.twig', ['words' => $wordRepository->search($term)]);
                    break;

                case 'expressions':
                    return $this->render('post/search.html.twig', ['expressions' => $expressionRepository->search($term)]);
                    break;

                case 'proverbes':
                    return $this->render('post/search.html.twig', ['proverbs' => $proverbRepository->search($term)]);
                    break;

                case 'blagues':
                    return $this->render('post/search.html.twig', ['jokes' => $jokeRepository->search($term)]);
                    break;

                case 'tout':
                    return $this->render('post/search.html.twig', [
                        'words'      => $wordRepository->search($term),
                        'expressions' => $expressionRepository->search($term),
                        'proverbs' => $proverbRepository->search($term),
                        'jokes' => $jokeRepository->search($term),
                    ]);
                    break;
            }
        }

        throw new \Exception('Cette publication est inconnue.');
    }

    /**
     * @param FormInterface $form
     * @param Word|Expression|Proverb|Joke $model
     * @param string $domain
     * @param string $addr
     * @return RedirectResponse
     */
    private function submitForm(FormInterface $form, $model, string $domain, string $addr): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /*
        dd($form);

        /*
        if ($form->getName() === 'joke') {
            $model->setDescription('blabla');
        }
        */

        $model->setSlug(Linguistic::toSlug($model->getPost()));
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
     * @param bool|null $question
     * @return PaginationInterface
     */
    private function getPaginator(PaginatorInterface $paginator, string $class, int $page, bool $question = null): PaginationInterface
    {
        /** @var QueryBuilder $query */
        $query = $this->getDoctrine()->getRepository($class)
            ->createQueryBuilder('q')
        ;

        if ($question) {
            $query
                ->andWhere('q.question = :question')
                ->setParameter('question', $question)
            ;
        }

        $query
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery();

        return $paginator->paginate($query, $page, AbstractPost::PAGINATOR_MAX);
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
}
