<?php

declare(strict_types=1);

namespace Api\Services;

use Api\Db\Entities\VerificationCode;
use Api\Db\Entities\User;
use Api\Db\Tables\VerificationCodes;
use Api\Db\Tables\VerificationAttempts;
use Api\Services\Generator\CodeGeneratorStrategy;
use Api\Services\SmsProvider\SmsProviderService;
use Core\Config;
use Core\Exceptions\CoreException;
use Core\Exceptions\HttpException;
use Core\Exceptions\ValidationException;

class CodeVerificationService
{
    private const THROTTLE              = 60; //1 min
    private const VERIFICATION_ATTEMPTS = 3;

    public function __construct(
        protected CodeGeneratorStrategy $codeGeneratorStrategy,
        protected SmsProviderService $smsProviderService
    ) {
    }

    /**
     * @param User $user
     * @return bool
     * @throws CoreException
     * @throws HttpException
     * @throws \JsonException
     */
    public function sendCode(User $user): bool
    {
        $latestCode = VerificationCodes::getInstance()->getLatestUserCode($user);

        if ($latestCode) {
            $throttle = (int)($this->getConfig('code_generation_throttle') ?? self::THROTTLE);
            if ($latestCode->getCreatedAt()?->getTimestamp() + $throttle > time()) {
                throw new HttpException('Please wait a minute', 429);
            }
        }

        return $this->smsProviderService->send($user, $this->generateCode($user)->getCode());
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getConfig(string $key): mixed
    {
        return Config::getInstance()->get('api')[$key] ?? null;
    }

    /**
     * @param User $user
     * @return VerificationCode
     * @throws CoreException
     * @throws \JsonException
     */
    protected function generateCode(User $user): VerificationCode
    {
        /**
         * @var VerificationCode $code
         */
        $code = VerificationCodes::getInstance()->fetchNew();
        $code->setUserId($user->getId());
        $code->setCode($this->codeGeneratorStrategy->generate());

        return VerificationCodes::getInstance()->save($code);
    }

    /**
     * @param User $user
     * @param string $code
     * @return bool
     * @throws CoreException
     * @throws HttpException
     * @throws \JsonException
     * @throws \Exception
     */
    public function verifyCode(User $user, string $code): bool
    {
        VerificationAttempts::getInstance()->logNewAttempt($user, $code);

        $latestCode = VerificationCodes::getInstance()->getLatestUserCode($user);

        if (!$latestCode || $latestCode->getIsUsed()) {
            throw (new ValidationException())->setErrors(['code' => 'No code issued']);
        }

        $latestCode->incrementAttempts();
        VerificationCodes::getInstance()->save($latestCode);

        $throttle = (int)($this->getConfig('code_verification_throttle') ?? self::THROTTLE);
        $attempts = (int)($this->getConfig('code_verification_attempts') ?? self::VERIFICATION_ATTEMPTS);

        if ($latestCode->getAttempts() > $attempts) {
            if (
                !$latestCode->getThrottledAt()
                || $latestCode->getThrottledAt()->getTimestamp() + $throttle > time()
            ) {
                $latestCode->setThrottledAt(new \DateTime());
                VerificationCodes::getInstance()->save($latestCode);
                throw new HttpException('Please wait a minute', 429);
            }

            //cool down period passed, another 3 attempts allowed
            $latestCode->setAttempts(1);
            $latestCode->setThrottledAt(null);
            VerificationCodes::getInstance()->save($latestCode);
        }

        if ($latestCode->getCode() !== $code) {
            return false;
        }

        $latestCode->setIsUsed(1);
        VerificationCodes::getInstance()->save($latestCode);

        $this->smsProviderService->send($user, 'Welcome to SMSBump');

        return true;
    }
}
