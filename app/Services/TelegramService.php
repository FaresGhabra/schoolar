<?php

namespace App\Services;

use GuzzleHttp\Client;

class TelegramService
{
    private $client;
    private $apiUrl;
    private $token;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = 'https://api.telegram.org/bot';
        $this->token = '5999727844:AAHTu1WCI3JxFvdkHr4rUjxiBenqNkAO8JQ';
    }

    public function sendMessage($chatId, $message)
    {
        $url = $this->apiUrl . $this->token . '/sendMessage';

        $response = $this->client->post($url, [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => $message,
            ],
        ]);

        return $response;
    }
}
