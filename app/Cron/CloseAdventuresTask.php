<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Orm\UserAdventure;

/**
 * CloseAdventureTask
 *
 * @author Jakub Konečný
 */
final class CloseAdventuresTask {
  use \Nette\SmartObject;

  protected \Nexendrie\Orm\Model $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @cronner-task(Close adventures)
   * @cronner-period(1 day)
   * @cronner-time(00:00 - 01:00)
   */
  public function run(): void {
    echo "Starting closing adventures ...\n";
    $adventures = $this->orm->userAdventures->findOpenAdventures();
    foreach($adventures as $adventure) {
      $adventure->progress = UserAdventure::PROGRESS_CLOSED;
      $this->orm->userAdventures->persistAndFlush($adventure);
    }
    echo "Finished closing adventures ...\n";
  }
}
?>