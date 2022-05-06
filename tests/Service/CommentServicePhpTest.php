<?php

namespace App\Tests\Service;

use App\Entity\Comment;
use App\Entity\News;
use App\Entity\User;
use App\Exception\NewsNotFoundException;
use App\Model\Comments\CommentItemListResponse;
use App\Model\Comments\CommentItemResponse;
use App\Repository\CommentRepository;
use App\Repository\NewsRepository;
use App\Service\CommentService;
use App\Tests\AbstractTestCase;

class CommentServicePhpTest extends AbstractTestCase
{
    private CommentRepository $commentRepository;
    private NewsRepository $newsRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->newsRepository = $this->createMock(NewsRepository::class);
    }

    public function testGetAllActiveFromWrongNews(): void
    {
        $this->newsRepository->expects($this->once())
            ->method('find')
            ->with(130)
            ->willReturn(null);

        $this->expectException(NewsNotFoundException::class);

        $this->createCommentService()->getAllActive(130);
    }

    public function testGetAllActiveFromNews(): void
    {
        $expectedNews = self::createNewsEntity();
        $expectedComment = self::createCommentEntity();

        $this->newsRepository->expects($this->once())
            ->method('find')
            ->with(130)
            ->willReturn($expectedNews);

        $this->commentRepository->expects($this->once())
            ->method('findOnlyActiveForNews')
            ->with($expectedNews)
            ->willReturn([$expectedComment]);

        $expectedItem = self::mapCommentItemToResponse();
        $expected = new CommentItemListResponse();
        $expected->setItems([$expectedItem]);

        $this->assertEquals($expected, $this->createCommentService()->getAllActive(130));
    }

    private function mapCommentItemToResponse(): CommentItemResponse
    {
        $comment = self::createCommentEntity();

        $mappedComment = new CommentItemResponse();
        $mappedComment->setId($comment->getId());
        $mappedComment->setBody($comment->getBody());
        $mappedComment->setOwnerName($comment->getOwner()->getName());
        $mappedComment->setCreatedAt($comment->getCreatedAt()->getTimestamp());

        return $mappedComment;
    }

    private function createCommentEntity(): Comment
    {
        $comment = new Comment();
        $this->setEntityId($comment, 1703);
        $comment->setOwner(self::createUserEntity());
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setBody('1');
        $comment->setActive(true);
        $comment->setTopic(self::createNewsEntity());

        return $comment;
    }

    private function createNewsEntity(): News
    {
        $news = new News();
        $this->setEntityId($news, 789);
        $news->setViewCount(1);
        $news->setDescription('1');
        $news->setCreatedAt(new \DateTimeImmutable());
        $news->setAnnotation('1');
        $news->setTitle('1');
        $news->setOwner(self::createUserEntity());

        return $news;
    }

    private function createUserEntity(): User
    {
        $user = new User();
        $user->setPassword('123');
        $user->setName('123');
        $user->setEmail('123');
        $user->setApiToken('123');
        $this->setEntityId($user, 123);

        return $user;
    }

    private function createCommentService(): CommentService
    {
        return new CommentService($this->commentRepository, $this->newsRepository);
    }
}
