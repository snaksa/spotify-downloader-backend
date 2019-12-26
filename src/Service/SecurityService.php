<?php declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Exception\NotAuthenticatedException;
use Symfony\Component\Security\Core\Security;

class SecurityService
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return User
     * @throws NotAuthenticatedException
     */
    public function getCurrentUser(): User
    {
        if ($this->security->getToken()) {
            $user = $this->security->getToken()->getUser();
            if ($user instanceof User) {
                return $user;
            }
        }

        throw new NotAuthenticatedException();
    }
}
