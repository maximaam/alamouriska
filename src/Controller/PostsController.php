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
use App\Entity\Proverbe;
use App\Form\MotType;
use App\Repository\LocutionRepository;
use App\Repository\MotRepository;
use App\Repository\ProverbeRepository;
use App\Utils\LikingUtils;
use App\Utils\Linguistic;
use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\CommentBundle\Model\Thread;
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
     * @Route("/mot", name="mot_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function motIndex(Request $request, \Swift_Mailer $mailer, PaginatorInterface $paginator): Response
    {

//        $message = (new \Swift_Message('Hello Email'))
//            //->setFrom('no-reply@sparheld.de')
//            ->setFrom('alamouriska.app@gmail.com', $this->getParameter('app_name'))
//            ->setTo('mimoberlino@gmail.com' )
//            ->setBody('<h1>test</h1>',
//                /*
//                $this->renderView(
//                // templates/emails/registration.html.twig
//                    'emails/registration.html.twig',
//                    ['name' => 'mimoberlino@gmail.com']
//                ),
//                */
//                'text/html'
//            )
//            /*
//             * If you also want to include a plaintext version of the message
//            ->addPart(
//                $this->renderView(
//                    'emails/registration.txt.twig',
//                    ['name' => $name]
//                ),
//                'text/plain'
//            )
//            */
//        ;
//
//        $mailer->send($message);

        $mot = (new Mot())->setUser($this->getUser());

        $form = $this->createForm(MotType::class, $mot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $mot->setSlug(Linguistic::toSlug($mot->getInLatin()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mot);
            $entityManager->flush();

            return $this->redirectToRoute('mot_index');
        }

        $likings = $this->getDoctrine()->getRepository(Liking::class)
            ->findBy(['owner' => 'mot']);

        $motsQuery = $this->getDoctrine()->getRepository(Mot::class)
            ->createQueryBuilder('m')
            ->where('m.status = :status')
            ->setParameter('status', Mot::STATUS_ACTIVE)
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

            $mot->setStatus(Mot::STATUS_DELETED);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show', ['username' => $this->getUser()->getUsername()]);
        }

        throw new \Exception('Cette publication est inconnue.');
    }

    /**
     * @Route("/locution", name="locution_index", methods={"GET","POST"})
     *
     * @param LocutionRepository $repository
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function locutionIndex(LocutionRepository $repository, Request $request, \Swift_Mailer $mailer): Response
    {

    }

    /**
     * @Route("/proverbe", name="proverbe_index", methods={"GET","POST"})
     *
     * @param ProverbeRepository $repository
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function proverbeIndex(ProverbeRepository $repository, Request $request, \Swift_Mailer $mailer): Response
    {

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

            /** @var \App\Entity\Thread $thread */
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
