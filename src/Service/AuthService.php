<?php declare(strict_types=1);

namespace App\Service;

use App\Constant\RequestMethods;
use App\Exception\SpotifyApiRequestException;
use App\Model\Auth;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthService
{
    /**
     * @var GuzzleService
     */
    private $guzzleService;

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
    private $redirectUrl;

    /**
     * SpotifyService constructor.
     *
     * @param GuzzleService $guzzleService
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUrl
     */
    public function __construct(GuzzleService $guzzleService, string $clientId, string $clientSecret, string $redirectUrl)
    {
        $this->guzzleService = $guzzleService;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @param string $code
     * @return Auth
     * @throws SpotifyApiRequestException
     */
    public function authenticateCallback(string $code)
    {
        $response = $this->guzzleService
            ->getApiClient('Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"))
            ->request(RequestMethods::POST, 'api/token', [
                RequestOptions::FORM_PARAMS => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUrl,
                ]
            ]);

        if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
            throw new SpotifyApiRequestException('Request to Spotify API failed', JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = json_decode($response->getBody()->getContents(), true);

        $auth = new Auth($content);

        return $auth;
    }
}
