<?php

namespace PkPayPlus\Resources;

use PkPayPlus\Client;

class Prices
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(string $merchantId, string $productId, array $data): array
    {
        return $this->client->request('POST', "/api/merchants/{$merchantId}/products/{$productId}/prices", [
            'json' => $data,
        ]);
    }

    public function list(string $merchantId, string $productId): array
    {
        return $this->client->request('GET', "/api/merchants/{$merchantId}/products/{$productId}/prices");
    }
}