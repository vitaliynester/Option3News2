<?php

namespace App\Tests\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentTest extends WebTestCase
{
    public function testCommentForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/news/1');
        $this->assertResponseIsSuccessful();

        $this->assertCount(0, $crawler->filter('form'));

        $client->clickLink('Войти');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Авторизация');

        $crawler = $client->request('GET', '/login');
        $loginButton = $crawler->selectButton('Войти');
        $form = $loginButton->form();
        $form['email'] = 'developer@mail.ru';
        $form['password'] = '123321';
        $client->submit($form);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Современные новости');
        $this->assertSelectorTextContains('.navbar-username', 'Здравствуй,');

        $crawler = $client->request('GET', '/news/1');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('form'));

        $sendButton = $crawler->selectButton('Отправить');
        $this->assertCount(0, $crawler->filter('.is-invalid'));
        $form = $sendButton->form();
        $form['comment[body]'] = '12';
        $client->submit($form);
        $this->assertSelectorTextContains('.text-danger', 'Длина комментария должна быть больше 3 символов!');

        $client->back();
        $crawler = $client->request('GET', '/news/1');
        $sendButton = $crawler->selectButton('Отправить');
        $form = $sendButton->form();
        $form['comment[body]'] = 'Тестовый коммент';
        $client->submit($form);
        $this->assertCount(0, $crawler->filter('.is-invalid'));
    }
}
