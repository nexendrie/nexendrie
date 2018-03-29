<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl,
    HeroesofAbenez\Chat\IDatabaseAdapter,
    Nette\Localization\ITranslator;

/**
 * OrderChatControl
 *
 * @author Jakub Konečný
 */
class OrderChatControl extends ChatControl {
  public function __construct(IDatabaseAdapter $databaseAdapter, \Nexendrie\Orm\Model $orm,  \Nette\Security\User $user, ITranslator $translator) {
    $userRecord = $orm->users->getById($user->id);
    $orderId = ($userRecord->order) ? $userRecord->order->id : 0;
    parent::__construct($databaseAdapter, "order", $orderId);
    $this->translator = $translator;
  }
}
?>