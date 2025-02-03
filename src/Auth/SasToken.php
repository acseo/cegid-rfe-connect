<?php

namespace App\Auth;

use App\Util\Sas;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SasToken
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        private Token $tokenService,
        private string $tokenUrl,
        private int $cacheDuration
    ) {
    }

    public function getSasToken(string $workspace, string $containerName, string $tenantId, int $minutesToLive = 1440, string $accessMode = 'ReadWrite'): Sas
    {
        return $this->cache->get("cegid_sastoken_{$workspace}_{$containerName}_{$tenantId}", function (ItemInterface $item) use ($workspace, $containerName, $tenantId, $minutesToLive, $accessMode) {
            $item->expiresAfter($this->cacheDuration);

            $url = sprintf(
                '%sstorage/api/%s/RFE/V1/getsastoken/%s?Minutes-To-Live=%d&AccessMode=%s',
                rtrim($this->tokenUrl, '/')."/",
                $workspace,
                $containerName,
                $minutesToLive,
                $accessMode
            );

            $token = $this->tokenService->getToken();

            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'X-TenantID' => $tenantId,
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $data = $response->toArray();

            if (!isset($data['blobServiceUri'], $data['containerName'], $data['sasToken'])) {
                throw new \RuntimeException('Failed to retrieve SAS token');
            }

            return new Sas($data['blobServiceUri'], $data['containerName'], $data['sasToken']);
        });
    }
}
