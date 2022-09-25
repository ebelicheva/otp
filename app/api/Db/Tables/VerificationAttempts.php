<?php

declare(strict_types=1);

namespace Api\Db\Tables;

use Api\Db\Entities\User;
use Api\Db\Entities\VerificationAttempt;
use Core\Db\Table;
use Core\Exceptions\CoreException;

/**
 * @method save(VerificationAttempt $entity): VerificationAttempt
 */
final class VerificationAttempts extends Table
{
    protected string $tableName = 'verification_attempts';

    protected string $entityClass = VerificationAttempt::class;

    /**
     * @param User $user
     * @param string $code
     * @return VerificationAttempt|null
     * @throws CoreException
     * @throws \JsonException
     */
    public function logNewAttempt(User $user, string $code): ?VerificationAttempt
    {
        /**
         * @var VerificationAttempt $attempt
         */
        $attempt = $this->fetchNew();

        $attempt->setUserId($user->getId());
        $attempt->setCode($code);

        return $this->save($attempt);
    }
}
