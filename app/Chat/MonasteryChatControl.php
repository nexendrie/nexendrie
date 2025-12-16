<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\DatabaseAdapter;
use Nexendrie\Orm\User;

/**
 * MonasteryChatControl
 *
 * @author Jakub Konečný
 */
final class MonasteryChatControl extends ChatControl
{
    public function __construct(DatabaseAdapter $databaseAdapter, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user)
    {
        /** @var User $userRecord */
        $userRecord = $orm->users->getById($user->id);
        $monasteryId = ($userRecord->monastery !== null) ? $userRecord->monastery->id : 0;
        parent::__construct($databaseAdapter, "monastery", $monasteryId, null, null);
    }
}
