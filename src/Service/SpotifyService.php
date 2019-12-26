<?php declare(strict_types=1);

namespace App\Service;

use App\Exception\SpotifyApiRequestException;
use App\Model\User;
use App\Model\Playlist;
use App\Model\Track;
use App\Constant\RequestMethods;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;

class SpotifyService
{
    /**
     * @var GuzzleService
     */
    private $guzzleService;

    /**
     * SpotifyService constructor.
     * @param GuzzleService $guzzleService
     */
    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    /**
     * @param string $token
     * @return User
     * @throws SpotifyApiRequestException
     */
    public function getUserInfo(string $token): User
    {
        $response = $this->guzzleService->getApiClient($token)->request(RequestMethods::GET, 'v1/me');

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
        $response = $this->guzzleService->getApiClient($user->getAccessToken())->request(RequestMethods::GET, "v1/playlists/{$id}?fields=id,name,tracks,images");

        $content = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException();
        }

        return new Playlist($content);
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
        // TODO: pass limit and page
        $response = $this->guzzleService->getApiClient($user->getAccessToken())->request(RequestMethods::GET, 'v1/me/playlists?fields=id,name,tracks,images');

        $content = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException();
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
        // TODO: pass limit and page
        $response = $this->guzzleService->getApiClient($user->getAccessToken())->request(RequestMethods::GET, "v1/playlists/{$id}/tracks");

        $content = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException();
        }

        $tracks = $content['items'];

        $result = [];
        foreach ($tracks as $track) {
            $result[] = new Track($track['track']);
        }

        return $result;
    }
}
