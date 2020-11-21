<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nextras\Orm\Collection\ICollection;

/**
 * Presenter Team
 *
 * @author Jakub Konečný
 */
class TeamPresenter extends BasePresenter {
  protected \Nexendrie\Orm\Model $orm;

  public function __construct(\Nexendrie\Orm\Model $orm) {
    parent::__construct();
    $this->orm = $orm;
  }

  public function renderDefault(): void {
    $this->template->admins = $this->orm->users->findBy([
      "group->level" => 10000,
    ])->orderBy("lastActive", ICollection::DESC);
  }

  protected function getDataModifiedTime(): int {
    $time = 0;
    /** @var \Nexendrie\Orm\User $admin */
    foreach($this->template->admins as $admin) {
      $time = max($time, $admin->lastActive);
    }
    return $time;
  }
}
?>