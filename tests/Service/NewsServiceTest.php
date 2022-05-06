<?php

namespace App\Tests\Service;

use App\Entity\News;
use App\Entity\User;
use App\Model\Comments\CommentItemResponse;
use App\Model\News\NewsItemListResponse;
use App\Model\News\NewsItemResponse;
use App\Repository\NewsRepository;
use App\Service\NewsService;
use App\Tests\AbstractTestCase;

class NewsServiceTest extends AbstractTestCase
{
    private NewsRepository $newsRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->newsRepository = $this->createMock(NewsRepository::class);
    }

    public function testGetAllNews(): void
    {
        $expectedNews = $this->createNewsEntity();

        $this->newsRepository->expects($this->once())
            ->method('findBy')
            ->with([], ['createdAt' => 'DESC', 'id' => 'DESC'])
            ->willReturn([$expectedNews]);

        $expectedItem = self::mapNewsItemToResponse($expectedNews);
        $expected = new NewsItemListResponse();
        $expected->setItems([$expectedItem]);

        $this->assertEquals($expected, $this->createNewsService()->getAllNews());
    }

    public function testGetAllNewsPerPage(): void
    {
        $expectedNews = $this->createNewsEntity();

        $this->newsRepository->expects($this->once())
            ->method('findPerPage')
            ->with(4)
            ->willReturn([$expectedNews]);

        $expectedItem = self::mapNewsItemToResponse($expectedNews);
        $expected = new NewsItemListResponse();
        $expected->setItems([$expectedItem]);

        $this->assertEquals($expected, $this->createNewsService()->getNewsPerPage(4));
    }

    public function testGetNewsById(): void
    {
        $expectedNews = $this->createNewsEntity();

        $this->newsRepository->expects($this->once())
            ->method('find')
            ->with(4)
            ->willReturn($expectedNews);

        $expected = self::mapNewsItemToResponse($expectedNews);

        $this->assertEquals($expected, $this->createNewsService()->getNewsById(4));
    }

    private function createNewsService(): NewsService
    {
        return new NewsService($this->newsRepository);
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
}
