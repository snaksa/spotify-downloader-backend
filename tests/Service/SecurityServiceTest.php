<?php

namespace App\Tests\Service;

use App\Exception\NotAuthenticatedException;
use App\Model\User;
use App\Service\SecurityService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class SecurityServiceTest extends TestCase
{
    /**
     * @test
     * @throws \App\Exception\NotAuthenticatedException
     */
    public function can_get_current_user()
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn((new User([]))->setName('Test'));

        $security = $this->createMock(Security::class);
        $security
            ->method('getToken')
            ->willReturn($token);

        $service = new SecurityService($security);
        $user = $service->getCurrentUser();

        $this->assertEquals('Test', $user->getName());
    }

    /**
     * @test
     * @throws \App\Exception\NotAuthenticatedException
     */
    public function can_not_get_not_authenticated_user()
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn((new User([]))->setName('Test'));

        $security = $this->createMock(Security::class);
        $security
            ->method('getToken')
            ->willReturn(null);

        $service = new SecurityService($security);

        $this->expectException(NotAuthenticatedException::class);

        $service->getCurrentUser();
    }
}
