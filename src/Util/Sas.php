<?php

namespace App\Util;

class Sas
{
    public function __construct(public string $blobServiceUri, public string $containerName, public string $sasToken)
    {
    }
}
