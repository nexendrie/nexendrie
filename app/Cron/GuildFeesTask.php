<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * GuildFeesTask
 *
 * @author Jakub Konečný
 */
class GuildFeesTask {
  use \Nette\SmartObject;
  
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Guild fees
   *
   * @cronner-task Guild fees
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  public function run(): void {
    $date = new \DateTime();
    $date->setTimestamp(time());
    if($date->format("j") != 1) {
      return;
    }
    echo "Starting paying guild fees ...\n";
    $users = $this->orm->users->findInGuild();
    foreach($users as $user) {
      $guildFee = $user->guildRank->guildFee;
      echo "$user->publicname (#$user->id} will pay {$guildFee} to his/her guild.\n";
      $user->money -= $guildFee;
      $user->guild->money += $guildFee;
      $this->orm->users->persistAndFlush($user);
    }
    echo "Finished paying guild fees ...\n";
  }
}
?>