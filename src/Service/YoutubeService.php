<?php

namespace App\Service;

use App\Constant\RequestMethods;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class YoutubeService
{
    /**
     * @var Client
     */
    private $apiClient;

    /**
     * @var string[]
     */
    private $clientIds;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * SpotifyService constructor.
     *
     * @param string $clientIds
     * @param string $baseUrl
     */
    public function __construct(string $clientIds, string $baseUrl)
    {
        $this->clientIds = explode('_APPEND_', $clientIds);
        $this->baseUrl = $baseUrl;
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
                    $response = $this->getApiClient()->request(RequestMethods::GET, "youtube/v3/search?part=snippet&type=video&q={$name}&key={$this->clientIds[$i]}");
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

    /**
     * @return Client
     */
    public function getApiClient()
    {
        if ($this->apiClient == null) {
            $this->apiClient = new Client([
                'base_uri' => $this->baseUrl,
                'http_errors' => false
            ]);
        }

        return $this->apiClient;
    }
}
