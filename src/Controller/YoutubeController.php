<?php

namespace App\Controller;

use App\Exception\NotAuthenticatedException;
use App\Service\SecurityService;
use App\Service\YoutubeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class YoutubeController extends BaseController
{
    /**
     * @var YoutubeService
     */
    protected $youtubeService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    public function __construct(SecurityService $securityService, YoutubeService $youtubeService)
    {
        $this->securityService = $securityService;
        $this->youtubeService = $youtubeService;
    }

    /**
     * @Route("/youtube/find", methods={"POST"}, name="api_youtube_find")
     * @param Request $request
     * @return JsonResponse
     */
    public function findSongs(Request $request)
    {
        try {
            // TODO: save activity
            $user = $this->securityService->getCurrentUser();
        } catch (NotAuthenticatedException $ex) {
            return new JsonResponse(['error' => 'Not authenticated.'], JsonResponse::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);
        $songs = array_key_exists('songs', $content) ? $content['songs'] : [];

        $urls = $this->youtubeService->findSongs($songs);

        return new JsonResponse($urls);
    }
}
