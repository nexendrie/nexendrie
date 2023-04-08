<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Orm\Order;
use Nexendrie\Orm\OrderFee;
use Nexendrie\Orm\OrderRank;

/**
 * OrderFeesTask
 *
 * @author Jakub Konečný
 */
final class OrderFeesTask extends BaseMonthlyCronTask {
  protected \Nexendrie\Orm\Model $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  protected function getFeeRecord(\Nexendrie\Orm\User $user): OrderFee {
    /** @var Order $order */
    $order = $user->order;
    $fee = $this->orm->orderFees->getByUserAndOrder($user, $order);
    if($fee !== null) {
      return $fee;
    }
    $fee = new OrderFee();
    $fee->user = $user;
    $fee->order = $order;
    return $fee;
  }
  
  /**
   * @cronner-task(Order fees)
   * @cronner-period(1 day)
   * @cronner-time(00:00 - 01:00)
   */
  public function run(): void {
    $date = new \DateTime();
    $date->setTimestamp(time());
    if(!$this->isDue($date)) {
      return;
    }
    echo "Starting paying order fees ...\n";
    $users = $this->orm->users->findInOrder();
    foreach($users as $user) {
      /** @var Order $order */
      $order = $user->order;
      /** @var OrderRank $orderRank */
      $orderRank = $user->orderRank;
      $orderFee = $orderRank->orderFee;
      echo "$user->publicname (#$user->id} will pay {$orderFee} to his/her order.\n";
      $user->money -= $orderFee;
      $order->money += $orderFee;
      $this->orm->users->persist($user);
      $fee = $this->getFeeRecord($user);
      $fee->amount += $orderFee;
      $this->orm->orderFees->persistAndFlush($fee);
    }
    echo "Finished paying order fees ...\n";
  }
}
?>