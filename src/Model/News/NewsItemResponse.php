<?php

namespace App\Model\News;

use App\Model\Comments\CommentItemResponse;

class NewsItemResponse
{
    private int $id;
    private string $title;
    private int $createdAt;
    private string $description;
    private int $viewCount;
    private string $annotation;
    /** @var CommentItemResponse[] */
    private array $comments;

    public function __construct()
    {
        $this->comments = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function setViewCount(int $viewCount): void
    {
        $this->viewCount = $viewCount;
    }

    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    public function setAnnotation(string $annotation): void
    {
        $this->annotation = $annotation;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }

    public function addComment(CommentItemResponse $comment): void
    {
        $this->comments[] = $comment;
    }
}
