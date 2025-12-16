<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\DatabaseAdapter;
use Nexendrie\Orm\User;

/**
 * GuildChatControl
 *
 * @author Jakub Konečný
 */
final class GuildChatControl extends ChatControl
{
    public function __construct(DatabaseAdapter $databaseAdapter, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user)
    {
        /** @var User $userRecord */
        $userRecord = $orm->users->getById($user->id);
        $guildId = ($userRecord->guild !== null) ? $userRecord->guild->id : 0;
        parent::__construct($databaseAdapter, "guild", $guildId, null, null);
    }
}
