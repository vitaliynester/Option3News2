<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Exception\NewsNotFoundException;
use App\Form\CommentType;
use App\Model\Comments\CommentItemListResponse;
use App\Model\ErrorResponse;
use App\Model\News\NewsItemListResponse;
use App\Model\News\NewsItemResponse;
use App\Repository\NewsRepository;
use App\Service\CommentService;
use App\Service\NewsService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class NewsController extends AbstractController
{
    public function __construct(private readonly NewsService $newsService,
                                private readonly CommentService $commentService,
                                private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route(path: '/news/{id}', name: 'news_detail', methods: ['GET', 'POST'])]
    public function show(int $id, NewsRepository $repository, Security $security, Request $request): Response
    {
        /** @var $user User */
        $user = $security->getUser();

        try {
            $news = $this->newsService->getNewsById($id);
        } catch (NewsNotFoundException) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        $newsEntity = $repository->find($news->getId());
        $comments = $this->commentService->getAllActive($news->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $commentBody = $form->get('body')->getData();

            $comment = new Comment();
            $comment->setOwner($user);
            $comment->setTopic($newsEntity);
            $comment->setBody($commentBody);

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('news_detail', [
                'id' => $news->getId(),
                'comments' => $comments
            ]);
        }

        if ($newsEntity->getOwner() !== $user) {
            $newsEntity->incrementViewCount();
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($newsEntity);
            $entityManager->flush();
        }

        return $this->render('home/detail-news.html.twig', [
            'news' => $news,
            'comments' => $comments->getItems(),
            'comment_form' => $form->createView(),
        ]);
    }


    #[Route(path: '/api/v1/news', name: 'api_news_all', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает список всех доступных новостей',
        content: new Model(type: NewsItemListResponse::class)
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Номер страницы для отображения',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    public function getAll(RequestStack $request): Response
    {
        $page = $request->getCurrentRequest()->query->get('page');

        if ($page === null) {
            return $this->json($this->newsService->getAllNews());
        }

        try {
            $pageInt = intval($page);
            return $this->json($this->newsService->getNewsPerPage(max($pageInt, 1)));
        } catch (Exception) {
            return $this->json($this->newsService->getNewsPerPage(1));
        }
    }

    #[Route('/api/v1/news/{id}', name: 'api_news_detail', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает детальную новость',
        content: new Model(type: NewsItemResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Указанная новость не найдена',
        content: new Model(type: ErrorResponse::class)
    )]
    public function getById(int $id): Response
    {
        try {
            return $this->json($this->newsService->getNewsById($id));
        } catch (NewsNotFoundException $exception) {
            return $this->json(new ErrorResponse($exception), $exception->getCode());
        }
    }

    #[Route('/api/v1/news/{id}/comments', name: 'api_news_comment', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает все комментарии к указанной новости',
        content: new Model(type: CommentItemListResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Указанная новость не найдена',
        content: new Model(type: ErrorResponse::class)
    )]
    public function getAllComments(int $id): Response
    {
        try {
            return $this->json($this->commentService->getAllActive($id));
        } catch (NewsNotFoundException $exception) {
            return $this->json(new ErrorResponse($exception), $exception->getCode());
        }
    }
}
