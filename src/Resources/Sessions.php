<?php

namespace PkPayPlus\Resources;

use PkPayPlus\Client;

class Sessions
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(string $merchantId, array $data): array
    {
        return $this->client->request('POST', "/merchant-api/{$merchantId}/checkout-sessions", [
            'json' => $data,
        ]);
    }

    public function verify(string $merchantId, string $sessionId): array
    {
        return $this->client->request('GET', "/merchant-api/{$merchantId}/{$sessionId}");
    }
}