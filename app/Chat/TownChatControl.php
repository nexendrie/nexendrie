<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl,
    HeroesofAbenez\Chat\IDatabaseAdapter,
    Nette\Localization\ITranslator;

/**
 * TownChat
 *
 * @author Jakub Konečný
 */
class TownChatControl extends ChatControl {
  public function __construct(IDatabaseAdapter $databaseAdapter, \Nette\Security\User $user, ITranslator $translator) {
    $townId = $user->identity->town;
    parent::__construct($databaseAdapter, "town", $townId);
    $this->translator = $translator;
  }
}
?>