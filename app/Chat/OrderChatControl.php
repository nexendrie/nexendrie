<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\DatabaseAdapter;
use Nexendrie\Orm\User;

/**
 * OrderChatControl
 *
 * @author Jakub Konečný
 */
final class OrderChatControl extends ChatControl
{
    public function __construct(DatabaseAdapter $databaseAdapter, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user)
    {
        /** @var User $userRecord */
        $userRecord = $orm->users->getById($user->id);
        $orderId = ($userRecord->order !== null) ? $userRecord->order->id : 0;
        parent::__construct($databaseAdapter, "order", $orderId, null, null);
    }
}
