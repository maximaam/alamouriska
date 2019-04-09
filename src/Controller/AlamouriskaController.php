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
use App\Form\MotType;
use App\Repository\LocutionRepository;
use App\Repository\MotRepository;
use App\Repository\ProverbeRepository;
use App\Utils\LikingUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\CommentBundle\Model\Thread;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;

/**
 * Class AlamouriskaController
 * @package App\Controller
 */
class AlamouriskaController extends AbstractController
{
    /**
     * @Route("/mot", name="mot_index", methods={"GET","POST"})
     *
     * @param MotRepository $repository
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function motIndex(MotRepository $repository, Request $request, \Swift_Mailer $mailer): Response
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

        $likings = $this->getDoctrine()->getRepository(Liking::class)
            ->findBy(['owner' => 'mot']);

        return $this->render('alamouriska/mot_index.html.twig', [
            'mot'   => $mot,
            'mots'  => $repository->findBy([], ['createdAt' => 'DESC']),
            'form'  => $form->createView(),
            'likings' => LikingUtils::getLikingsUsersIds($likings)
        ]);
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
     * @Route("/mot/{id}", name="mot_show", methods={"GET"})
     *
     * @param Mot $mot
     * @param Request $request
     * @param ThreadManagerInterface $threadManager
     * @param CommentManagerInterface $commentManager
     * @return Response
     * @throws \ReflectionException
     */
    public function motShow(Mot $mot, Request $request, ThreadManagerInterface $threadManager, CommentManagerInterface $commentManager): Response
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

        return $this->render('alamouriska/mot_show.html.twig', [
            'mot' => $mot,
            'comments' => $comments,
            'thread' => $thread,
        ]);
    }

}
