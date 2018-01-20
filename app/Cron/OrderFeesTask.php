<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * OrderFeesTask
 *
 * @author Jakub Konečný
 */
class OrderFeesTask extends BaseMonthlyCronTask {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @cronner-task Order fees
   * @cronner-period 1 day
   * @cronner-time 00:00 - 01:00
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
      $orderFee = $user->orderRank->orderFee;
      echo "$user->publicname (#$user->id} will pay {$orderFee} to his/her order.\n";
      $user->money -= $orderFee;
      $user->order->money += $orderFee;
      $this->orm->users->persistAndFlush($user);
    }
    echo "Finished paying order fees ...\n";
  }
}
?>