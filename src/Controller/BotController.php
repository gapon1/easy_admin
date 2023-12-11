<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use App\Service\BinanceApi;
use App\Service\SlackSendingMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BotController extends AbstractController
{
    #[Route('/bot', name: 'app_bot')]
    public function index(ItemRepository $itemRepository, SlackSendingMessage $sendingMessage, BinanceApi $binanceApi): Response
    {
        $items = $itemRepository->findAllItems();
        $binance = $binanceApi->binanceApiInfo($items, $sendingMessage);
        var_dump($binance);
        die();

        return $this->render('bot/index.html.twig', [
            'text' => 'text',
        ]);
    }
}
