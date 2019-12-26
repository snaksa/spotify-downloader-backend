<?php

namespace App\Tests\Service;

use App\Service\GuzzleService;
use PHPUnit\Framework\TestCase;

class GuzzleServiceTest extends TestCase
{
    /**
     * @test
     */
    public function can_get_client_without_authentication_header()
    {
        $service = new GuzzleService('baseUri');
        $client = $service->getApiClient();
        $config = $client->getConfig();

        $this->assertEquals('baseUri', $config['base_uri']->getPath());
        $this->assertArrayNotHasKey('Authorization', $config['headers']);
    }

    /**
     * @test
     */
    public function can_get_client_with_authentication_header()
    {
        $service = new GuzzleService('baseUri');
        $client = $service->getApiClient('token');
        $config = $client->getConfig();

        $this->assertEquals('baseUri', $config['base_uri']->getPath());
        $this->assertArrayHasKey('Authorization', $config['headers']);
    }
}
