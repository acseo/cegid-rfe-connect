# acseo/cegid-rfe-connect

This PHP package provides functionality to interact with Azure Blob Storage using SAS tokens. It includes authentication, listing files in a folder, and retrieving a file from storage.

## 📦 Installation

To install this package, run:

```sh
composer require acseo/cegid-rfe-connect
```

This will install all required dependencies, including:
- `symfony/http-client`
- `symfony/cache`
- `symfony/dotenv` (for testing)
- `phpunit/phpunit` (for testing)

## 🚀 Usage

### Authentication
The `Auth\Token` class handles authentication and retrieves an access token from the API.

```php
$httpClient = HttpClient::create();
$cache = new FilesystemAdapter();

$tokenService = new Auth\Token($httpClient, $cache, $clientId, $username, $password, $tokenUrl, $cacheDuration);
$token = $auth->getToken();
```

### Fetching a SAS Token
The `Auth\SasToken` class retrieves a SAS token to access Azure Blob Storage.

```php
$sasTokenService = new Auth\SasToken($httpClient, $cache, $tokenService, $sasTokenUrl, $cacheDuration);
$sasToken = $sasTokenService->getSasToken($tenantId, $storagePath);
```

### Listing Files in a Folder
The `RFE\Folder\ListFiles` class lists files within a given folder in the blob storage.

```php
$listFiles = new RFE\Folder\ListFiles($httpClient, $sasToken);
$files = $listFiles->listFiles($'some-folder/');
```

### Retrieving a File
The `RFE\File\GetFile` class fetches the contents of a file stored in Azure Blob Storage.

```php
$getFile = new RFE\File\GetFile($httpClient, $sasToken);
$fileContent = $getFile->getFile('some-folder/myfile.txt');
```

## 🧪 Running Tests

Create a `.env.test` file by copying the `.env.test.dist` template:

```sh
cp .env.test.dist .env.test
```

Then, update `.env.test` with the required values:

```sh
CLIENT_ID=your_client_id
CLIENT_SECRET=your_client_secret
USERNAME=your_username
PASSWORD=your_password
TOKEN_URL=your_token_url
CACHE_DURATION=3600
SAS_TOKEN_URL=your_sas_token_url
TENANT_ID=your_tenant_id
STORAGE_PATH=your_storage_path
FILE_PATH=your_file_path
FOLDER_PATH=your_folder_path
```

To execute the PHPUnit test suite, run:

```sh
composer test
```

This will run unit tests for authentication, SAS token retrieval, file listing, and file fetching.

---

### 📌 Notes
- Ensure your `.env.test` file contains valid credentials before running tests.
- Check the Azure Blob Storage permissions when using SAS tokens.

### 🔗 References
- [Azure Storage REST API](https://learn.microsoft.com/en-us/rest/api/storageservices/)
- [Symfony HttpClient](https://symfony.com/doc/current/components/http_client.html)
- [PHPUnit Documentation](https://phpunit.de/)

Enjoy using `acseo/cegid-rfe-connect`! 🚀
