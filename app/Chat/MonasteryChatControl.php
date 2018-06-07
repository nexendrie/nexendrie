<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\IDatabaseAdapter;
use Nette\Localization\ITranslator;

/**
 * MonasteryChatControl
 *
 * @author Jakub Konečný
 */
final class MonasteryChatControl extends ChatControl {
  public function __construct(IDatabaseAdapter $databaseAdapter, \Nexendrie\Orm\Model $orm,  \Nette\Security\User $user, ITranslator $translator) {
    $userRecord = $orm->users->getById($user->id);
    $monasteryId = ($userRecord->monastery) ? $userRecord->monastery->id : 0;
    parent::__construct($databaseAdapter, "monastery", $monasteryId, null, null, $translator);
  }
}
?>