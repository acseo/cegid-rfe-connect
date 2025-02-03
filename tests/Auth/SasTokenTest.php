<?php

namespace Tests\Auth;

use App\Auth\Token;
use App\Auth\SasToken;
use App\Util\Sas;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;

class SasTokenTest extends TestCase
{
    private Token $tokenService;
    private SasToken $sasTokenService;

    protected function setUp(): void
    {
        // Charger les variables d'environnement
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env.test');

        $httpClient = HttpClient::create();
        $cache = new FilesystemAdapter();

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
    }

    public function testGetSasToken(): void
    {
        $tenantId = $_ENV['TENANT_ID'];
        $workspace = $_ENV['WORKSPACE'];
        $containerName = $_ENV['CONTAINER_NAME'];

        $sasToken = $this->sasTokenService->getSasToken($workspace, $containerName, $tenantId);

        $this->assertInstanceOf(Sas::class, $sasToken);
        $this->assertNotEmpty($sasToken->blobServiceUri);
        $this->assertNotEmpty($sasToken->containerName);
        $this->assertNotEmpty($sasToken->sasToken);
    }
}
