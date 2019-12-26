<?php declare(strict_types=1);

namespace App\Service;

use App\Constant\RequestMethods;
use App\Exception\SpotifyApiRequestException;
use App\Model\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthService
{
    /**
     * @var Client
     */
    private $accountsClient;

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
     * @var string
     */
    private $redirectUrl;

    /**
     * SpotifyService constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $baseUrl
     * @param string $redirectUrl
     */
    public function __construct(string $clientId, string $clientSecret, string $baseUrl, string $redirectUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->baseUrl = $baseUrl;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @param string $code
     * @return Auth
     * @throws SpotifyApiRequestException
     */
    public function authenticateCallback(string $code)
    {
        try {
            $response = $this->getAccountsClient()->request(RequestMethods::POST, 'api/token', [
                RequestOptions::FORM_PARAMS => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUrl,
                ]
            ]);
        }
        catch (GuzzleException $ex) {
            throw new SpotifyApiRequestException('Request to Spotify API failed', JsonResponse::HTTP_BAD_REQUEST);
        }

        if($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException('Request to Spotify API failed', JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = json_decode($response->getBody()->getContents(), true);

        $auth = new Auth($content);

        return $auth;
    }

    /**
     * @param string $authToken
     * @return Client
     */
    public function getAccountsClient()
    {
        if ($this->accountsClient == null) {
            $this->accountsClient = new Client([
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
                ],
                'http_errors' => false
            ]);
        }

        return $this->accountsClient;
    }
}
