<?php

declare(strict_types=1);

namespace Api\Services\SmsProvider;

use Api\Db\Entities\Message;
use Api\Db\Entities\User;
use Api\Db\Tables\Messages;

class DbMockService implements SmsProviderService
{
    /**
     * @param User $user
     * @param string $message
     * @return bool
     */
    public function send(User $user, string $message): bool
    {
        $messagesTable = Messages::getInstance();

        /**
         * @var Message $messageEntity
         */
        $messageEntity = $messagesTable->fetchNew();
        $messageEntity->setMessage($message);
        $messageEntity->setUserId($user->getId());

        try {
            /**
             * @var Message $messageEntity
             */
            $messageEntity = $messagesTable->save($messageEntity);

            return (bool)$messageEntity->getId();
        } catch (\Exception $exception) {
            //TODO: Log exception
            return true;
        }
    }
}
