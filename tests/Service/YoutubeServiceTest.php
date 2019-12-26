<?php

namespace App\Tests\Service;

use App\Constant\RequestMethods;
use App\Service\GuzzleService;
use App\Service\YoutubeService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class YoutubeServiceTest extends TestCase
{
    /**
     * @test
     */
    public function can_get_search_result()
    {
        $client = $this->createMock(Client::class);
        $client
            ->expects($this->at(0))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search1',
                    'key' => 'apiKey'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['items' => [['id' => ['videoId' => '1']]]])))
            );

        $client
            ->expects($this->at(1))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search2',
                    'key' => 'apiKey'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['items' => [['id' => ['videoId' => '2']]]])))
            );

        $guzzleService = $this->createMock(GuzzleService::class);
        $guzzleService
            ->method('getApiClient')
            ->willReturn($client);

        $service = new YoutubeService('apiKey', $guzzleService);
        $foundTracks = $service->findSongs([
            'key1' => 'search1',
            'key2' => 'search2'
        ]);

        $this->assertEquals([
            'key1' => '1',
            'key2' => '2'
        ], $foundTracks);
    }

    /**
     * @test
     */
    public function can_get_search_result_after_youtube_quota_key_reached()
    {
        $client = $this->createMock(Client::class);
        $client
            ->expects($this->at(0))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search1',
                    'key' => 'apiKey'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['items' => [['id' => ['videoId' => '1']]]])))
            );

        $client
            ->expects($this->at(1))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search2',
                    'key' => 'apiKey'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['error' => []])))
            );

        $client
            ->expects($this->at(2))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search2',
                    'key' => 'apiKey2'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['items' => [['id' => ['videoId' => '2']]]])))
            );

        $guzzleService = $this->createMock(GuzzleService::class);
        $guzzleService
            ->method('getApiClient')
            ->willReturn($client);

        $service = new YoutubeService('apiKey_APPEND_apiKey2', $guzzleService);
        $foundTracks = $service->findSongs([
            'key1' => 'search1',
            'key2' => 'search2'
        ]);

        $this->assertEquals([
            'key1' => '1',
            'key2' => '2'
        ], $foundTracks);
    }

    /**
     * @test
     */
    public function can_not_get_search_result_after_all_youtube_quota_key_reached()
    {
        $client = $this->createMock(Client::class);
        $client
            ->expects($this->at(0))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search1',
                    'key' => 'apiKey'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['items' => [['id' => ['videoId' => '1']]]])))
            );

        $client
            ->expects($this->at(1))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search2',
                    'key' => 'apiKey'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['error' => []])))
            );

        $client
            ->expects($this->at(2))
            ->method('request')
            ->with(
                RequestMethods::GET,
                "youtube/v3/search", [
                'query' => [
                    'part' => 'snippet',
                    'type' => 'video',
                    'q' => 'search2',
                    'key' => 'apiKey2'
                ]
            ])
            ->willReturn(
                (new Response(200, [], json_encode(['error' => []])))
            );

        $guzzleService = $this->createMock(GuzzleService::class);
        $guzzleService
            ->method('getApiClient')
            ->willReturn($client);

        $service = new YoutubeService('apiKey_APPEND_apiKey2', $guzzleService);
        $foundTracks = $service->findSongs([
            'key1' => 'search1',
            'key2' => 'search2'
        ]);

        $this->assertEquals([
            'key1' => '1'
        ], $foundTracks);
    }
}
