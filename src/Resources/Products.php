<?php

namespace PkPayPlus\Resources;

use PkPayPlus\Client;

class Products
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function list(array $query = []): array
    {
        return $this->client->request('GET', "/merchant-api/products", [
            'query' => $query,
        ]);
    }

    public function create(array $data): array
    {
        return $this->client->request('POST', "/merchant-api/products", [
            'json' => $data,
        ]);
    }

    public function get(string $productId): array
    {
        return $this->client->request('GET', "/merchant-api/products/{$productId}");
    }

    public function update(string $productId, array $data): array
    {
        return $this->client->request('PUT', "/merchant-api/products/{$productId}", [
            'json' => $data,
        ]);
    }

    public function delete(string $productId): array
    {
        return $this->client->request('DELETE', "/merchant-api/products/{$productId}");
    }
}