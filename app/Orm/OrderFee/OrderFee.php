<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * OrderFee
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$orderFees}
 * @property Order $order {m:1 Order::$fees}
 * @property int $amount {default 0}
 * @property int $created
 */
final class OrderFee extends BaseEntity {
  
}
?>