<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Exception\UserAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Model\ErrorResponse;
use App\Model\User\SignInRequest;
use App\Model\User\SignUpRequest;
use App\Model\User\SignUpResponse;
use App\Service\AuthorizationService;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OAA;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    public function __construct(private AuthorizationService $signUpService)
    {
    }

    #[Route(path: '/api/v1/auth/login', name: 'api_auth_login', methods: ['POST'])]
    #[OA\Response(
        response: 400,
        description: 'Указанные данные некорректны!',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Пользователь успешно авторизован',
        content: new Model(type: SignUpResponse::class)
    )]
    /**
     * @OAA\RequestBody (
     *    @Model(type=SignInRequest::class)
     * )
     */
    public function signIn(#[RequestBody] SignInRequest $signInRequest): Response
    {
        try {
            return $this->json($this->signUpService->signIn($signInRequest));
        } catch (UserNotFoundException $exception) {
            return $this->json(new ErrorResponse($exception), $exception->getCode());
        }
    }

    #[Route(path: '/api/v1/auth/register', name: 'api_auth_register', methods: ['POST'])]
    #[OA\Response(
        response: 409,
        description: 'Пользователь с указанным Email уже существует!',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Ошибка в теле запроса!',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 201,
        description: 'Пользователь успешно создан',
        content: new Model(type: SignUpResponse::class)
    )]
    /**
     * @OAA\RequestBody (
     *    @Model(type=SignUpRequest::class)
     * )
     */
    public function signUp(#[RequestBody] SignUpRequest $signUpRequest): Response
    {
        try {
            return $this->json($this->signUpService->signUp($signUpRequest), 201);
        } catch (UserAlreadyExistsException|Exception $exception) {
            return $this->json(new ErrorResponse($exception), $exception->getCode());
        }
    }
}
