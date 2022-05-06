<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Современные новости');
        $this->assertCount(68, $crawler->filter('.news-card'));
        $client->clickLink('Название новости 68');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Название новости 68');
    }
}
