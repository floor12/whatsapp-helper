<?php

namespace floor12\whatsapp;

use GuzzleHttp\Client;

class WhatsappHelper
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(string $apiToken, string $apiUrl, Client $preconfiguredClient = null)
    {

        $this->client = $preconfiguredClient ?? new Client([
                'base_uri' => $apiUrl,
                'timeout' => 2.0,
                'headers' => ['Access-Token' => $apiToken]
            ]);
    }

    public function getUnreadCount(): int
    {
        try {
            $response = $this->client->get('/unread-number');
            if ($responseJson = json_decode($response->getBody()->getContents())) {
                if ($responseJson->status == 'success' && $responseJson->unread_chats_count) {
                    return (int)$responseJson->unread_chats_count;
                }
            }
            return 0;
        } catch (\Throwable $e) {
            throw new WhatsappHelperException($e->getMessage());
        }

    }

    public function checkNumber(): bool
    {
        try {
            $response = $this->client->get('/check-phone');
            if ($responseJson = json_decode($response->getBody()->getContents())) {
                if ($responseJson->status == 'success' && $responseJson->phone_has_whatsapp) {
                    return (bool)$responseJson->phone_has_whatsapp;
                }
            }
            return false;
        } catch (\Throwable $e) {
            throw new WhatsappHelperException($e->getMessage());
        }

    }

}