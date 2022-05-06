<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testAuthorization(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $client->clickLink('Войти');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Авторизация');

        $crawler = $client->request('GET', '/login');
        $loginButton = $crawler->selectButton('Войти');
        $form = $loginButton->form();
        $form['email'] = 'test123@mail.ru';
        $form['password'] = 'test123321';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertSelectorTextSame('.alert', 'Invalid credentials.');
        $loginButton = $crawler->selectButton('Войти');
        $form = $loginButton->form();
        $form['email'] = 'developer@mail.ru';
        $form['password'] = '123321';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Современные новости');
        $this->assertSelectorTextContains('.navbar-username', 'Здравствуй,');
    }
}
