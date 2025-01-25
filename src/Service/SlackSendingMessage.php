<?php

namespace App\Service;

use JoliCode\Slack\ClientFactory;

class SlackSendingMessage
{

    protected $slackToken;
    protected $slackChannel;

    public function __construct($slackToken, $slackChannel)
    {
        $this->slackToken = $slackToken;
        $this->slackChannel = $slackChannel;
    }

    public function sendMessage(string $params): string
    {
        $client = ClientFactory::create($this->slackToken);
        $chat = $client->chatPostMessage(
            [
                'channel' => '#'.$this->slackChannel,
                'text' => $params
            ]);

        return $chat->getMessage()->getText();
    }

}