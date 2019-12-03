<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Journal;
use App\Entity\LatestPosts;
use App\Entity\Page;
use App\Entity\Rating;
use App\Entity\User;
use App\Form\JournalType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
     * @param CacheInterface $cache
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function index(Request $request, CacheInterface $cache): Response
    {
        /*
        $response = $cache->get('index_index', function (ItemInterface $item) use ($request) {
            $item->expiresAfter(3600);

            $response =  $this->render('index/index.html.twig', [
                'journal_form' => $this->createForm(JournalType::class, new Journal())->createView(),
                'journals' => $this->getDoctrine()->getRepository(Journal::class)->findBy([], ['id' => 'DESC'], 20),
                'latest_posts' => $this->getDoctrine()->getRepository(LatestPosts::class)->findBy([], [], 10),
                'latest_comments' => $this->getDoctrine()->getRepository(Comment::class)->findBy([], ['id' => 'DESC'], 10),
                'ratings' => $this->getDoctrine()->getRepository(Rating::class)->findAll(),
                'has_rated' => null !== $this->getDoctrine()->getRepository(Rating::class)->findOneBy(['addr' => $request->getClientIp()]),
                'page' => $this->getDoctrine()->getRepository(Page::class)->findOneBy(['alias' => 'homepage']),
                'latest_users'  => $this->getDoctrine()->getRepository(User::class)->findBy(['enabled' => true], ['id' => 'DESC'], 6),
            ]);

            $response->headers->setCookie(new Cookie('jumbotron', 'done', \strtotime('now + 1 week')));

            return $response;
        });
        */

        $response =  $this->render('index/index.html.twig', [
            'journal_form' => $this->createForm(JournalType::class, new Journal())->createView(),
            'journals' => $this->getDoctrine()->getRepository(Journal::class)->findBy([], ['id' => 'DESC'], 20),
            'latest_posts' => $this->getDoctrine()->getRepository(LatestPosts::class)->findBy([], [], 10),
            'latest_comments' => $this->getDoctrine()->getRepository(Comment::class)->findBy([], ['id' => 'DESC'], 10),
            'ratings' => $this->getDoctrine()->getRepository(Rating::class)->findAll(),
            'has_rated' => null !== $this->getDoctrine()->getRepository(Rating::class)->findOneBy(['addr' => $request->getClientIp()]),
            'page' => $this->getDoctrine()->getRepository(Page::class)->findOneBy(['alias' => 'homepage']),
            'latest_users'  => $this->getDoctrine()->getRepository(User::class)->findBy(['enabled' => true], ['id' => 'DESC'], 6),
        ]);

        $response->headers->setCookie(new Cookie('jumbotron', 'done', \strtotime('now + 1 week')));

        return $response;
    }

    /**
     * @Route("/page/{alias}", name="index_page")
     *
     * @param Page $page
     * @return Response
     */
    public function page(Page $page): Response
    {
        return $this->render('index/page.html.twig', [
            'page'   => $page,
        ]);
    }
}
