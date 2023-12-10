<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BotController extends AbstractController
{
    #[Route('/bot', name: 'app_bot')]
    public function index(): Response
    {
        return $this->render('bot/index.html.twig', [
            'controller_name' => 'BotController',
        ]);
    }
}
