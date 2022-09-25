<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Db\Entities\User;
use Api\Db\Tables\Users;
use Api\Request\CodeVerificationRequest;
use Api\Request\RefreshCodeRequest;
use Api\Request\RegistrationRequest;
use Api\Services\CodeVerificationService;
use Core\Auth\Services\JwtTokenService;
use Core\Controllers\Attributes\Route;
use Core\Controllers\BaseApiController;
use Core\Exceptions\CoreException;
use Core\Exceptions\HttpException;
use Core\Exceptions\ValidationException;
use Core\Response\JsonResponse;
use JsonException;

class AuthController extends BaseApiController
{
    /**
     * @param RegistrationRequest $request
     * @param CodeVerificationService $codeVerificationService
     * @param JwtTokenService $jwtTokenService
     * @return JsonResponse
     * @throws CoreException
     * @throws HttpException
     * @throws JsonException
     */
    #[Route(name: 'register', path: '/api/v1/register', methods: ['post'])]
    public function register(
        RegistrationRequest $request,
        CodeVerificationService $codeVerificationService,
        JwtTokenService $jwtTokenService
    ): JsonResponse {
        $data = $request->getSanitizedData();
        unset($data['id']);

        $usersTable = Users::getInstance();

        $user = $usersTable->fetchNew();
        $user->fromArray($data);

        /**
         * @var User $user
         */
        $user = $usersTable->save($user);

        if (!$codeVerificationService->sendCode($user)) {
            return $this->sendResponse([
                'error' => 'VerificationCode not sent. Probably invalid phone number.'
            ], 400);
        }

        return $this->sendResponse(['token' => $jwtTokenService->getToken($user)]);
    }

    /**
     * @param CodeVerificationRequest $request
     * @param CodeVerificationService $codeVerificationService
     * @return JsonResponse
     * @throws CoreException
     * @throws HttpException
     * @throws JsonException
     * @throws ValidationException
     */
    #[Route(name: 'verify-code', path: '/api/v1/verify-code', methods: ['post'])]
    public function verifyCode(
        CodeVerificationRequest $request,
        CodeVerificationService $codeVerificationService
    ): JsonResponse {
        if (!$codeVerificationService->verifyCode($request->getUser(), $request->getData()['code'] ?? '')) {
            throw (new ValidationException())->setErrors(['code' => 'Invalid code']);
        }

        return $this->sendResponse([
            'success' => true,
            'message' => 'Welcome to SMS Bump'
        ]);
    }

    /**
     * @param RefreshCodeRequest $request
     * @param CodeVerificationService $codeVerificationService
     * @return JsonResponse
     * @throws CoreException
     * @throws HttpException
     * @throws JsonException
     */
    #[Route(name: 'refresh-code', path: '/api/v1/refresh-code', methods: ['post'])]
    public function refreshCode(
        RefreshCodeRequest $request,
        CodeVerificationService $codeVerificationService
    ): JsonResponse {
        if (!$codeVerificationService->sendCode($request->getUser())) {
            return $this->sendResponse([
                'error' => 'VerificationCode not sent. Please try again later.'
            ], 400);
        }

        return $this->sendResponse(['success' => true]);
    }
}
