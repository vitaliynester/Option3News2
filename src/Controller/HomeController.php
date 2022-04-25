<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(NewsRepository $repository): Response
    {
        $newsList = $repository->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'news_list' => $newsList,
        ]);
    }

    #[Route(path: '/news/{id}', name: 'news_detail', methods: ['GET', 'POST'])]
    public function show(int $id, CommentRepository $commentRepository, NewsRepository $repository, Security $security, Request $request): Response
    {
        /** @var $user User */
        $user = $security->getUser();

        $news = $repository->findOneBy(['id' => $id]);
        if ($news === null) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        $comments = $commentRepository->findOnlyActiveForNews($news);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentBody = $form->get('body')->getData();

            $comment = new Comment();
            $comment->setOwner($user);
            $comment->setTopic($news);
            $comment->setBody($commentBody);

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('news_detail', [
                'id' => $news->getId(),
                'comments' => $comments
            ]);
        }

        if ($news->getOwner() !== $user) {
            $news->incrementViewCount();
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($news);
            $entityManager->flush();
        }

        return $this->render('home/detail-news.html.twig', [
            'news' => $news,
            'comments' => $comments,
            'comment_form' => $form->createView(),
        ]);
    }
}
