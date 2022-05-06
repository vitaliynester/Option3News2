<?php

namespace App\Service;

use App\Entity\News;
use App\Exception\NewsNotFoundException;
use App\Model\Comments\CommentItemResponse;
use App\Model\News\NewsItemListResponse;
use App\Model\News\NewsItemResponse;
use App\Repository\NewsRepository;

class NewsService
{
    public function __construct(private readonly NewsRepository $newsRepository)
    {
    }

    public function getAllNews(): NewsItemListResponse
    {
        $newsList = $this->newsRepository->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC']);

        $mappedNews = array_map(array($this, 'mapNewsItemToResponse'), $newsList);

        $result = new NewsItemListResponse();
        $result->setItems($mappedNews);

        return $result;
    }

    public function getNewsPerPage(int $page): NewsItemListResponse
    {
        $newsList = $this->newsRepository->findPerPage($page);

        $mappedNews = array_map(array($this, 'mapNewsItemToResponse'), $newsList);

        $result = new NewsItemListResponse();
        $result->setItems($mappedNews);

        return $result;
    }

    public function getNewsById(int $id): NewsItemResponse
    {
        $news = $this->newsRepository->find($id);
        if ($news === null) {
            throw new NewsNotFoundException();
        }

        return self::mapNewsItemToResponse($news);
    }

    private function mapNewsItemToResponse(News $news): NewsItemResponse
    {
        $mappedNews = new NewsItemResponse();
        $mappedNews->setId($news->getId());
        $mappedNews->setTitle($news->getTitle());
        $mappedNews->setAnnotation($news->getAnnotation());
        $mappedNews->setCreatedAt($news->getCreatedAt()->getTimestamp());
        $mappedNews->setDescription($news->getDescription());
        $mappedNews->setViewCount($news->getViewCount());

        $comments = $news->getComments();
        foreach ($comments as $comment) {
            $mappedComment = new CommentItemResponse();
            $mappedComment->setId($comment->getId());
            $mappedComment->setBody($comment->getBody());
            $mappedComment->setOwnerName($comment->getOwner()->getName());
            $mappedComment->setCreatedAt($comment->getCreatedAt()->getTimestamp());

            $mappedNews->addComment($mappedComment);
        }

        return $mappedNews;
    }
}
