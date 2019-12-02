<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Controller;

use App\Entity\AbstractPost;
use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\Deleted;
use App\Entity\Joke;
use App\Entity\Liking;
use App\Entity\Expression;
use App\Entity\Word;
use App\Entity\Proverb;
use App\Form\CommentType;
use App\Repository\JokeRepository;
use App\Repository\ExpressionRepository;
use App\Repository\WordRepository;
use App\Repository\ProverbRepository;
use App\Utils\LikingUtils;
use App\Utils\Linguistic;
use App\Utils\ModelUtils;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{ RedirectResponse, Request, Response };
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

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
     * @param CacheInterface $cache
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(Request $request, PaginatorInterface $paginator, CacheInterface $cache): Response
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

        $response = $cache->get('post_index_posts_' . $entity, function(ItemInterface $item) use ($request, $form, $entity, $domain, $model, $paginator) {
            $item->expiresAfter(3600);

            $pageId = $request->query->getInt('page', 1);
            $isEnigma = $request->query->getBoolean('enigmatique', false);

            $response = $this->render('post/index.html.twig', [
                'domain' => $domain,
                'entity' => $entity,
                'posts'  => $this->getPaginator($paginator, \get_class($model), $pageId, $isEnigma),
                'likings' => $this->getLikings($entity),
                'form'  => $form->createView(),
            ]);

            return $response;
        });

        return $response;

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
                case 'mots-algeriens':
                    return $this->render('post/search.html.twig', ['words' => $wordRepository->search($term)]);
                    break;

                case 'expressions-algeriennes':
                    return $this->render('post/search.html.twig', ['expressions' => $expressionRepository->search($term)]);
                    break;

                case 'proverbes-algeriens':
                    return $this->render('post/search.html.twig', ['proverbs' => $proverbRepository->search($term)]);
                    break;

                case 'blagues-algeriennes':
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

                default:
                    throw new \Exception('Cette publication est inconnue.');
            }
        }

        throw new \Exception('Cette publication est inconnue.');
    }

    /**
     * @param FormInterface $form
     * @param Word|Expression|Proverb|Joke|Blog $model
     * @param string $domain
     * @param string $addr
     * @return RedirectResponse
     */
    private function submitForm(FormInterface $form, $model, string $domain, string $addr): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $model->setSlug(\substr(Linguistic::toSlug($model->getPost()), 0, AbstractPost::SLUG_LIMIT));
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
