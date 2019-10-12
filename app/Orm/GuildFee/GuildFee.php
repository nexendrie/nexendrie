<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * GuildFee
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$guildFees}
 * @property Guild $guild {m:1 Guild::$fees}
 * @property int $amount {default 0}
 */
final class GuildFee extends BaseEntity {
  
}
?>