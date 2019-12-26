<?php

namespace App\Tests\Service;

use App\Exception\SpotifyApiRequestException;
use App\Service\GuzzleService;
use App\Service\SpotifyService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class SpotifyServiceTest extends TestCase
{
    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_get_user_info()
    {
        $responseBody = [
            'id' => 'ID',
            'display_name' => 'Test User',
            'email' => 'test@gmail.com',
            'country' => 'USA',
            'external_urls' => [
                'spotify' => 'www.test.com'
            ],
            'images' => []
        ];

        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn(
            (new Response(200, [], json_encode($responseBody)))
        );

        $guzzleService = $this->createMock(GuzzleService::class);
        $guzzleService
            ->method('getApiClient')
            ->with('token')
            ->willReturn($client);

        $service = new SpotifyService($guzzleService);
        $user = $service->getUserInfo('token');

        $this->assertEquals($responseBody['id'], $user->getId());
    }

    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_not_get_user_info_invalid_status()
    {
        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn(
            (new Response(500, [], ''))
        );

        $guzzleService = $this->createMock(GuzzleService::class);
        $guzzleService
            ->method('getApiClient')
            ->with('token')
            ->willReturn($client);

        $service = new SpotifyService($guzzleService);

        $this->expectException(SpotifyApiRequestException::class);

        $service->getUserInfo('token');
    }
}
