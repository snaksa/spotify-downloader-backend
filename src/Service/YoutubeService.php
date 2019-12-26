<?php declare(strict_types=1);

namespace App\Service;

use App\Constant\RequestMethods;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class YoutubeService
{
    /**
     * @var string[]
     */
    private $clientIds;

    /**
     * @var GuzzleService
     */
    private $guzzleService;

    /**
     * SpotifyService constructor.
     *
     * @param string $clientIds
     * @param GuzzleService $guzzleService
     */
    public function __construct(string $clientIds, GuzzleService $guzzleService)
    {
        $this->clientIds = explode('_APPEND_', $clientIds);
        $this->guzzleService = $guzzleService;
    }

    /**
     * @param array $names
     * @return string[]
     */
    public function findSongs(array $names): array
    {
        $clientIdsCount = count($this->clientIds);
        $result = [];
        foreach ($names as $key => $name) {
            try {
                for ($i = 0; $i < $clientIdsCount; $i++) {
                    $response = $this->guzzleService->getApiClient()->request(RequestMethods::GET, "youtube/v3/search", [
                        'query' => [
                            "part" => "snippet",
                            "type" => "video",
                            'q' => $name,
                            'key' => $this->clientIds[$i]
                        ]
                    ]);
                    $content = json_decode($response->getBody()->getContents(), true);

                    if (array_key_exists('error', $content)) {
                        if ($i + 1 === $clientIdsCount) {
                            return $result;
                        }
                    } else {
                        $items = $content['items'];
                        $id = count($items) > 0 ? $items[0]['id']['videoId'] : null;

                        $result[$key] = $id;
                        break;
                    }
                }
            } catch (GuzzleException $ex) {
                $result[$key] = null;
            }
        }

        return $result;
    }
}
