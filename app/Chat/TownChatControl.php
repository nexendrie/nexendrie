<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\DatabaseAdapter;

/**
 * TownChat
 *
 * @author Jakub Konečný
 */
final class TownChatControl extends ChatControl {
  public function __construct(DatabaseAdapter $databaseAdapter, \Nette\Security\User $user) {
    $townId = $user->identity->town;
    parent::__construct($databaseAdapter, "town", $townId, null, null);
  }
}
?>