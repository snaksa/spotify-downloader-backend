<?php

namespace App\Service;

use App\Exception\SpotifyApiRequestException;
use App\Model\User;
use App\Model\Playlist;
use App\Model\Track;
use App\Constant\RequestMethods;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;

class SpotifyService
{
    /**
     * @var Client
     */
    private $apiClient;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * SpotifyService constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(string $clientId, string $clientSecret, string $baseUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $token
     * @return User
     * @throws SpotifyApiRequestException
     */
    public function getUserInfo(string $token): User
    {
        try {
            $response = $this->getApiClient($token)->request(RequestMethods::GET, 'v1/me');
        }
        catch (GuzzleException $ex) {
            throw new SpotifyApiRequestException('Request to Spotify API failed', JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException();
        }

        $user = new User($content);
        $user->setAccessToken($token);

        return $user;
    }

    /**
     * @param User $user
     * @param string $id
     * @return Playlist
     * @throws SpotifyApiRequestException
     */
    public function getPlaylist(User $user, string $id): Playlist
    {
        try {
            $response = $this->getApiClient($user->getAccessToken())->request(RequestMethods::GET, "v1/playlists/{$id}?fields=id,name,tracks,images");
        }
        catch (GuzzleException $ex) {
            throw new SpotifyApiRequestException('Request to Spotify API failed', JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException($content);
        }

        $result = new Playlist($content);

        return $result;
    }

    /**
     * @param User $user
     * @param int $limit
     * @param int $page
     * @return Playlist[]
     * @throws SpotifyApiRequestException
     */
    public function getPlaylists(User $user, int $limit, int $page): array
    {
        try {
            // TODO: pass limit and page
            $response = $this->getApiClient($user->getAccessToken())->request(RequestMethods::GET, 'v1/me/playlists?fields=id,name,tracks,images');
        }
        catch (GuzzleException $ex) {
            throw new SpotifyApiRequestException('Request to Spotify API failed', JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException($content);
        }

        $playlists = $content['items'];

        $result = [];
        foreach ($playlists as $playlist) {
            $result[] = new Playlist($playlist);
        }

        return $result;
    }

    /**
     * @param User $user
     * @param string $id
     * @param int $limit
     * @param int $page
     * @return array
     * @throws SpotifyApiRequestException
     */
    public function getPlaylistTracks(User $user, string $id, int $limit, int $page)
    {
        try {
            // TODO: pass limit and page
            $response = $this->getApiClient($user->getAccessToken())->request(RequestMethods::GET, "v1/playlists/{$id}/tracks");
        }
        catch (GuzzleException $ex) {
            throw new SpotifyApiRequestException('Request to Spotify API failed', JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException($content);
        }

        $tracks = $content['items'];

        $result = [];
        foreach ($tracks as $track) {
            $result[] = new Track($track['track']);
        }

        return $result;
    }

    /**
     * @param string $authToken
     * @return Client
     */
    public function getApiClient(string $authToken)
    {
        if ($this->apiClient == null) {
            $this->apiClient = new Client([
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => $authToken,
                ],
                'http_errors' => false
            ]);
        }

        return $this->apiClient;
    }
}
