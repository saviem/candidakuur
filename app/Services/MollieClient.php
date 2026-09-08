<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MollieClient
{
    public function configured(): bool
    {
        return filled(config('services.mollie.key'));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createPayment(array $payload): array
    {
        return $this->request('post', '/v2/payments', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayment(string $id): array
    {
        return $this->request('get', '/v2/payments/'.$id);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $payload = []): array
    {
        if (! $this->configured()) {
            throw new RuntimeException('Mollie is nog niet geconfigureerd.');
        }

        $http = Http::baseUrl(rtrim((string) config('services.mollie.api_url'), '/'))
            ->withToken((string) config('services.mollie.key'))
            ->acceptJson()
            ->asJson()
            ->timeout(30);

        $response = $method === 'get'
            ? $http->get($path)
            : $http->post($path, $payload);

        if ($response->failed()) {
            throw new RuntimeException('Mollie-betaling mislukt: '.$response->body());
        }

        /** @var array<string, mixed> $json */
        $json = $response->json();

        return $json;
    }
}
