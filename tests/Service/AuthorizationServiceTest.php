<?php

namespace App\Tests\Service;

use App\Exception\UserAlreadyExistsException;
use App\Model\User\SignUpRequest;
use App\Repository\UserRepository;
use App\Service\AuthorizationService;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthorizationServiceTest extends AbstractTestCase
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }


    public function testSignUpWithExistsEmail(): void
    {
        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@mail.ru')
            ->willReturn(true);

        $this->expectException(UserAlreadyExistsException::class);

        $signUpRequest = self::createSignUpRequest();

        $this->createAuthorizationService()->signUp($signUpRequest);
    }

    private function createAuthorizationService(): AuthorizationService
    {
        return new AuthorizationService($this->userRepository, $this->hasher, $this->entityManager);
    }

    private function createSignUpRequest(): SignUpRequest
    {
        $signUpRequest = new SignUpRequest();
        $signUpRequest->setEmail('test@mail.ru');
        $signUpRequest->setName('test');
        $signUpRequest->setPassword('test12399');
        $signUpRequest->setConfirmPassword('test12388');

        return $signUpRequest;
    }
}
