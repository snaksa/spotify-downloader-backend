<?php

namespace App\Model;

use App\Interfaces\SerializableInterface;

class Auth implements SerializableInterface
{
    /**
     * @var string
     */
    private $access_token;

    /**
     * @var string
     */
    private $refresh_token;

    /**
     * @var string
     */
    private $scope;

    /**
     * Auth constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->access_token = $data['access_token'];
        $this->refresh_token = array_key_exists('refresh_token', $data) ? $data['refresh_token'] : null;
        $this->scope = $data['scope'];
    }

    public function serialize(): array
    {
        return [
            'accessToken' => $this->getAccessToken(),
            'refreshToken' => $this->getRefreshToken(),
            'scope' => $this->getScope(),
        ];
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @param string $access_token
     */
    public function setAccessToken(string $access_token): void
    {
        $this->access_token = $access_token;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    /**
     * @param string $refresh_token
     */
    public function setRefreshToken(?string $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     */
    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }
}
