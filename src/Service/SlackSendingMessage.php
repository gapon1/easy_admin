<?php

namespace App\Service;

use JoliCode\Slack\ClientFactory;

class SlackSendingMessage
{

    protected $slackToken;

    public function __construct($slackToken)
    {
        $this->slackToken = $slackToken;
    }

    public function sendMessage(string $params): string
    {
        $client = ClientFactory::create($this->slackToken);
        $chat = $client->chatPostMessage(
            [
                'channel' => '#build-bot',
                'text' => $params
            ]);

        return $chat->getMessage()->getText();
    }

}