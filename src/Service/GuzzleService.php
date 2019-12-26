<?php declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;

class GuzzleService
{
    /**
     * @var Client
     */
    private $apiClient;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var string
     */
    private $authToken;

    /**
     * GuzzleService constructor.
     *
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param string $authToken
     * @return Client
     */
    public function getApiClient(string $authToken = null)
    {
        if ($this->apiClient == null || $this->authToken !== $authToken) {
            $config = [
                'base_uri' => $this->baseUri,
                'http_errors' => false
            ];

            if ($authToken) {
                $config['headers'] = [
                    'Authorization' => $authToken,
                ];

                $this->authToken = $authToken;
            }

            $this->apiClient = new Client($config);
        }

        return $this->apiClient;
    }
}
