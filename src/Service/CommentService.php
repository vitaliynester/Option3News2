<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\News;
use App\Exception\NewsNotFoundException;
use App\Model\Comments\CommentItemListResponse;
use App\Model\Comments\CommentItemResponse;
use App\Repository\CommentRepository;
use App\Repository\NewsRepository;

class CommentService
{
    public function __construct(private readonly CommentRepository $commentRepository,
                                private readonly NewsRepository $newsRepository)
    {
    }

    public function getAllActive(int $newsId): CommentItemListResponse
    {
        $news = $this->newsRepository->find($newsId);
        if ($news === null) {
            throw new NewsNotFoundException();
        }

        $comments = $this->commentRepository->findOnlyActiveForNews($news);

        $mappedComments = array_map(array($this, 'mapCommentItemToResponse'),$comments);

        $result = new CommentItemListResponse();
        $result->setItems($mappedComments);

        return $result;
    }

    private function mapCommentItemToResponse(Comment $comment): CommentItemResponse
    {
        $mappedComment = new CommentItemResponse();
        $mappedComment->setId($comment->getId());
        $mappedComment->setBody($comment->getBody());
        $mappedComment->setOwnerName($comment->getOwner()->getName());
        $mappedComment->setCreatedAt($comment->getCreatedAt()->getTimestamp());

        return $mappedComment;
    }
}
