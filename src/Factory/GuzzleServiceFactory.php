<?php declare(strict_types=1);

namespace App\Factory;

use App\Service\GuzzleService;

class GuzzleServiceFactory
{
    public function __invoke(string $baseUri)
    {
        return new GuzzleService($baseUri);
    }
}
