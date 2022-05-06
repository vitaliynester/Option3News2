<?php

namespace App\Controller;

use App\Service\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private readonly NewsService $newsService)
    {
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        $newsList = $this->newsService->getAllNews();

        return $this->render('home/index.html.twig', [
            'news_list' => $newsList->getItems(),
        ]);
    }
}
