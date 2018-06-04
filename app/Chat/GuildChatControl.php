<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\IDatabaseAdapter;

/**
 * GuildChatControl
 *
 * @author Jakub Konečný
 */
final class GuildChatControl extends ChatControl {
  public function __construct(IDatabaseAdapter $databaseAdapter, \Nexendrie\Orm\Model $orm,  \Nette\Security\User $user) {
    $userRecord = $orm->users->getById($user->id);
    $guildId = ($userRecord->guild) ? $userRecord->guild->id : 0;
    parent::__construct($databaseAdapter, "guild", $guildId);
  }
}
?>