<?php

namespace App\RFE\File;

use App\Util\Sas;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetFile
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Sas $sasTokenData
    ) {
    }

    public function getFile(string $filePath): string
    {
        $blobServiceUri = rtrim($this->sasTokenData->blobServiceUri, '/') . '/';
        $containerName = $this->sasTokenData->containerName;
        $sasToken = ltrim($this->sasTokenData->sasToken, '?');

        $url = sprintf(
            '%s%s/%s?%s',
            $blobServiceUri,
            $containerName,
            $filePath,
            $sasToken
        );

        $response = $this->httpClient->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to retrieve file: ' . $response->getStatusCode());
        }

        return $response->getContent();
    }
}
