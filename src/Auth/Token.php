<?php

namespace App\Auth;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Token
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        private string $clientId,
        private string $username,
        private string $password,
        private string $tokenUrl,
        private int $cacheDuration = 3600,
        private string $scope = 'RetailBackendApi offline_access'
    ) {
    }

    public function getToken(): string
    {
        return $this->cache->get('cegid_auth_token', function (ItemInterface $item) {
            $item->expiresAfter($this->cacheDuration); // Expiration après durée définie

            $response = $this->httpClient->request('POST', $this->tokenUrl, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    'client_id' => $this->clientId,
                    'username' => $this->username,
                    'password' => $this->password,
                    'grant_type' => 'password',
                    'scope' => $this->scope
                ],
            ]);

            $data = $response->toArray();

            return $data['access_token'] ?? throw new \RuntimeException('Failed to retrieve access token');
        });
    }
}
