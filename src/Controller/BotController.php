<?php

namespace App\Controller;

use JoliCode\Slack\ClientFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BotController extends AbstractController
{
    #[Route('/bot', name: 'app_bot')]
    public function index(): Response
    {
        $slackToken = $this->getParameter('slack_token');
        $client = ClientFactory::create($slackToken);
        $chat = $client->chatPostMessage(
            [
                'channel' => '#bot',
                'text' => 'Hello Chalio' . rand(10, 200)
            ]);

        $text = $chat->getMessage()->getText();

        return $this->render('bot/index.html.twig', [
            'text' => $text,
        ]);
    }
}
