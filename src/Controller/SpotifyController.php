<?php


namespace App\Controller;

use App\Exception\NotAuthenticatedException;
use App\Exception\SpotifyApiRequestException;
use App\Service\SecurityService;
use App\Service\SpotifyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class SpotifyController extends BaseController
{
    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @var SpotifyService
     */
    protected $spotifyService;

    public function __construct(SecurityService $securityService, SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
        $this->securityService = $securityService;
    }

    /**
     * @Route("/me", name="api_spotify_me")
     */
    public function me()
    {
        try {
            $user = $this->securityService->getCurrentUser();
        } catch (NotAuthenticatedException $ex) {
            return new JsonResponse('Not authenticated.', JsonResponse::HTTP_FORBIDDEN);
        }

        return new JsonResponse($this->item($user));
    }

    /**
     * @Route("/me/playlists", name="api_spotify_playlists")
     */
    public function playlists()
    {
        try {
            $user = $this->securityService->getCurrentUser();
        } catch (NotAuthenticatedException $ex) {
            return new JsonResponse(['error' => 'Not authenticated.'], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $playlists = $this->spotifyService->getPlaylists($user, 100, 1);
        } catch (SpotifyApiRequestException $ex) {
            return new JsonResponse($ex->getMessage());
        }

        return new JsonResponse($this->collection($playlists));
    }

    /**
     * @Route("/playlists/{id}/tracks", name="api_spotify_playlist_tracks")
     * @param string $id
     * @return JsonResponse
     */
    public function tracks(string $id)
    {
        try {
            $user = $this->securityService->getCurrentUser();
        } catch (NotAuthenticatedException $ex) {
            return new JsonResponse('Not authenticated.', JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $playlist = $this->spotifyService->getPlaylist($user, $id);
            $playlists = $this->spotifyService->getPlaylistTracks($user, $id, $playlist->getTotalTracks(), 1);
        } catch (SpotifyApiRequestException $ex) {
            return new JsonResponse($ex->getMessage());
        }

        return new JsonResponse($this->collection($playlists));
    }
}
