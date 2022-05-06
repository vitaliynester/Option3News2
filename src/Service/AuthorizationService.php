<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Model\User\SignInRequest;
use App\Model\User\SignUpResponse;
use App\Model\User\SignUpRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthorizationService
{
    public function __construct(private UserRepository $userRepository,
                                private readonly UserPasswordHasherInterface $hasher,
                                private readonly EntityManagerInterface $em)
    {
    }

    public function signUp(SignUpRequest $signUpRequest): SignUpResponse
    {
        if ($this->userRepository->existsByEmail($signUpRequest->getEmail())) {
            throw new UserAlreadyExistsException();
        }

        $user = new User();
        $user->setEmail($signUpRequest->getEmail());
        $user->setName($signUpRequest->getName());
        $user->setPassword($this->hasher->hashPassword($user, $signUpRequest->getPassword()));
        $user->setApiToken(sha1(uniqid()));
        $this->em->persist($user);
        $this->em->flush();

        return new SignUpResponse($user->getId(), $user->getApiToken());
    }

    public function signIn(SignInRequest $signInRequest): SignUpResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $signInRequest->getEmail()]);
        if (null === $user) {
            throw new UserNotFoundException();
        }
        if (!$this->hasher->isPasswordValid($user, $signInRequest->getPassword())) {
            throw new UserNotFoundException();
        }

        return new SignUpResponse($user->getId(), $user->getApiToken());
    }
}
