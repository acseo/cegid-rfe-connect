<?php

namespace App\RFE\Folder;

use App\Util\Sas;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ListFiles
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Sas $sasTokenData
    ) {
    }

    public function listFiles(string $folderPath): array
    {
        $blobServiceUri = rtrim($this->sasTokenData->blobServiceUri, '/') . '/';
        $containerName = $this->sasTokenData->containerName;
        $sasToken = ltrim($this->sasTokenData->sasToken, '?');

        $url = sprintf(
            '%s%s?restype=container&comp=list&prefix=%s&%s',
            $blobServiceUri,
            $containerName,
            $folderPath,
            $sasToken
        );

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
        ]);

        $content = $response->getContent();

        $xml = simplexml_load_string($content);

        if ($xml === false) {
            throw new \RuntimeException('Failed to parse Azure Blob Storage response');
        }

        $files = [];
        foreach ($xml->Blobs->Blob as $blob) {
            $files[] = (string) $blob->Name;
        }

        return $files;
    }
}
