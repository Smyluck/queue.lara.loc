<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Laravel API', version: '1.0.0')]
#[OA\Server(url: '/', description: 'Default server')]
final class ApiSpec
{
    // This class exists only for OpenAPI attributes scanning.
}
