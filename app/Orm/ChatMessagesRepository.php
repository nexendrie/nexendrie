<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ChatMessagesRepository
 *
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<ChatMessage>
 */
final class ChatMessagesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [ChatMessage::class];
    }
}
