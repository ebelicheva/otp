<?php

declare(strict_types=1);

namespace Api\Db\Tables;

use Api\Db\Entities\VerificationCode;
use Api\Db\Entities\User;
use Core\Db\Table;
use Core\Exceptions\CoreException;

/**
 * @method save(VerificationCode $entity): VerificationCode
 */
final class VerificationCodes extends Table
{
    protected string $tableName = 'verification_codes';

    protected string $entityClass = VerificationCode::class;

    /**
     * @param User $user
     * @return VerificationCode|null
     * @throws CoreException
     */
    public function getLatestUserCode(User $user): ?VerificationCode
    {
        $latestCodes = $this->findBy(
            ['user_id' => $user->getId()],
            ['id' => 'desc'],
            1
        );

        return $latestCodes[0] ?? null;
    }
}
