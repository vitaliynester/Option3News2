<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;

class NewsControllerTest extends AbstractControllerTest
{
    public function testGetWithoutAuthorizationHeader(): void
    {
        $this->client->request('GET', '/api/v1/news');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetAllNews(): void
    {
        $this->client->request('GET', '/api/v1/news', [], [], self::getHeaderWithToken());
        $this->assertResponseIsSuccessful();

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'createdAt', 'description', 'viewCount', 'annotation', 'comments'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'createdAt' => ['type' => 'integer'],
                            'description' => ['type' => 'string'],
                            'viewCount' => ['type' => 'integer'],
                            'annotation' => ['type' => 'string'],
                            'comments' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => ['id', 'createdAt', 'body', 'ownerName'],
                                    'properties' => [
                                        'id' => ['type' => 'integer'],
                                        'createdAt' => ['type' => 'integer'],
                                        'body' => ['type' => 'string'],
                                        'ownerName' => ['type' => 'string']
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testGetNewsById(): void
    {
        $this->client->request('GET', '/api/v1/news/1', [], [], self::getHeaderWithToken());
        $this->assertResponseIsSuccessful();

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id', 'title', 'createdAt', 'description', 'viewCount', 'annotation', 'comments'],
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'string'],
                'createdAt' => ['type' => 'integer'],
                'description' => ['type' => 'string'],
                'viewCount' => ['type' => 'integer'],
                'annotation' => ['type' => 'string'],
                'comments' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'createdAt', 'body', 'ownerName'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'createdAt' => ['type' => 'integer'],
                            'body' => ['type' => 'string'],
                            'ownerName' => ['type' => 'string']
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testGetAllCommentsByNewsId(): void
    {
        $this->client->request('GET', '/api/v1/news/1/comments', [], [], self::getHeaderWithToken());
        $this->assertResponseIsSuccessful();

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'createdAt', 'body', 'ownerName'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'createdAt' => ['type' => 'integer'],
                            'body' => ['type' => 'string'],
                            'ownerName' => ['type' => 'string']
                        ],
                    ]
                ],
            ],
        ]);
    }

    public function getHeaderWithToken(): array
    {
        return [
            'HTTP_X_AUTH_TOKEN' => 'adminToken',
        ];
    }
}
