<?php

namespace App\Tests\Service;

use App\Exception\SpotifyApiRequestException;
use App\Model\User;
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
        $this->assertEquals($responseBody['display_name'], $user->getName());
        $this->assertEquals($responseBody['email'], $user->getEmail());
        $this->assertEquals($responseBody['country'], $user->getCountry());
        $this->assertEquals($responseBody['external_urls']['spotify'], $user->getUrl());
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

    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_get_playlist()
    {
        $responseBody = [
            'id' => 'ID',
            'name' => 'Test User',
            'tracks' => [
                'total' => 10
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
        $playlist = $service->getPlaylist((new User([]))->setAccessToken('token'), 'playlist-id');

        $this->assertEquals($responseBody['id'], $playlist->getId());
        $this->assertEquals($responseBody['name'], $playlist->getName());
        $this->assertEquals($responseBody['tracks']['total'], $playlist->getTotalTracks());
    }

    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_not_get_playlist_invalid_status()
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

        $service->getPlaylist((new User([]))->setAccessToken('token'), 'playlist-id');
    }

    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_get_playlists()
    {
        $response = [
            'items' => [
                [
                    'id' => 'ID',
                    'name' => 'Test Playlist',
                    'tracks' => [
                        'total' => 10
                    ],
                    'images' => []
                ],
                [
                    'id' => 'ID2',
                    'name' => 'Test Playlist',
                    'tracks' => [
                        'total' => 10
                    ],
                    'images' => []
                ]
            ]
        ];

        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn(
            (new Response(200, [], json_encode($response)))
        );

        $guzzleService = $this->createMock(GuzzleService::class);
        $guzzleService
            ->method('getApiClient')
            ->with('token')
            ->willReturn($client);

        $service = new SpotifyService($guzzleService);
        $playlists = $service->getPlaylists((new User([]))->setAccessToken('token'), 10, 1);

        foreach ($playlists as $key => $playlist) {
            $this->assertEquals($response['items'][$key]['id'], $playlist->getId());
            $this->assertEquals($response['items'][$key]['name'], $playlist->getName());
            $this->assertEquals($response['items'][$key]['tracks']['total'], $playlist->getTotalTracks());
        }
    }

    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_not_get_playlists_invalid_status()
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

        $service->getPlaylists((new User([]))->setAccessToken('token'), 10, 1);
    }

    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_get_playlist_tracks()
    {
        $response = [
            'items' => [
                [
                    'track' => [
                        'id' => 'ID',
                        'name' => 'Test Track',
                        'artists' => [
                            'Artist 1',
                            'Artist 2'
                        ]
                    ]
                ],
                [
                    'track' => [
                        'id' => 'ID2',
                        'name' => 'Test Track',
                        'artists' => [
                            'Artist 1',
                            'Artist 2'
                        ]
                    ]
                ],
            ]
        ];

        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn(
            (new Response(200, [], json_encode($response)))
        );

        $guzzleService = $this->createMock(GuzzleService::class);
        $guzzleService
            ->method('getApiClient')
            ->with('token')
            ->willReturn($client);

        $service = new SpotifyService($guzzleService);
        $tracks = $service->getPlaylistTracks((new User([]))->setAccessToken('token'), 'id', 10, 1);

        foreach ($tracks as $key => $track) {
            $this->assertEquals($response['items'][$key]['track']['id'], $track->getId());
            $this->assertEquals($response['items'][$key]['track']['name'], $track->getName());
            $this->assertEquals($response['items'][$key]['track']['artists'], $track->getArtists());
        }
    }

    /**
     * @test
     * @throws \App\Exception\SpotifyApiRequestException
     */
    public function can_not_get_playlist_tracks_invalid_status()
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

        $service->getPlaylistTracks((new User([]))->setAccessToken('token'), 'id', 10, 1);
    }
}
