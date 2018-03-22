<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl,
    HeroesofAbenez\Chat\IDatabaseAdapter;

/**
 * TownChat
 *
 * @author Jakub Konečný
 */
class TownChatControl extends ChatControl {
  public function __construct(IDatabaseAdapter $databaseAdapter, \Nette\Security\User $user) {
    $townId = $user->identity->town;
    parent::__construct($databaseAdapter, "town", $townId);
  }
}
?>