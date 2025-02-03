<?php

namespace Tests\RFE\File;

use App\Auth\SasToken;
use App\Auth\Token;
use App\RFE\Folder\ListFiles;
use PHPUnit\Framework\TestCase;
use App\RFE\File\GetFile;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;

class GetFileTest extends TestCase
{
    private Token $tokenService;
    private SasToken $sasTokenService;
    private ListFiles $listFilesService;
    private GetFile $getFileService;

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
        $this->getFileService = new GetFile($httpClient, $sas);
    }

    public function testGetFile(): void
    {
        $folderPrefix = $_ENV['FOLDER_PREFIX'];
        $files = $this->listFilesService->listFiles($folderPrefix);

        foreach ($files as $file) {
            $fileContent = $this->getFileService->getFile($file);
            $this->assertIsString($fileContent);
            break;
        }
    }
}
