<?php

namespace Tests\RFE\Folder;

use App\Auth\SasToken;
use App\Auth\Token;
use PHPUnit\Framework\TestCase;
use App\RFE\Folder\ListFiles;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;

class ListTest extends TestCase
{
    private Token $tokenService;
    private SasToken $sasTokenService;
    private ListFiles $listFilesService;

    protected function setUp(): void
    {
        // Charger les variables d'environnement
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../../.env.test');

        $cache = new FilesystemAdapter();
        $httpClient = HttpClient::create();


        $this->tokenService = new Token(
            $httpClient,
            $cache,
            $_ENV['CLIENT_ID'],
            $_ENV['USERNAME'],
            $_ENV['PASSWORD'],
            $_ENV['TOKEN_URL'],
            (int) $_ENV['CACHE_DURATION']
        );

        $this->sasTokenService = new SasToken(
            $httpClient,
            $cache,
            $this->tokenService,
            $_ENV['SAS_TOKEN_URL'],
            (int) $_ENV['CACHE_DURATION']
        );


        $tenantId = $_ENV['TENANT_ID'];
        $workspace = $_ENV['WORKSPACE'];
        $containerName = $_ENV['CONTAINER_NAME'];

        $sas = $this->sasTokenService->getSasToken($workspace, $containerName, $tenantId);
        $this->listFilesService = new ListFiles($httpClient, $sas);
    }

    public function testListFiles(): void
    {
        $folderPrefix = $_ENV['FOLDER_PREFIX'];

        $files = $this->listFilesService->listFiles($folderPrefix);

        $this->assertIsArray($files);
        foreach ($files as $file) {
            $this->assertIsString($file);
            $this->assertNotEmpty($file);
        }
    }
}
