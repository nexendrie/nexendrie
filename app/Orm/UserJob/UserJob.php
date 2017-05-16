<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * UserJob
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$jobs}
 * @property Job $job {m:1 Job::$userJobs}
 * @property int $started
 * @property bool $finished {default false}
 * @property int|NULL $lastAction {default NULL}
 * @property int $count {default 0}
 * @property int $earned {default 0}
 * @property int $extra {default 0}
 * @property-read int $finishTime {virtual}
 */
class UserJob extends \Nextras\Orm\Entity\Entity {
  protected function getterFinishTime(): int {
    return $this->started + (60 * 60 * 24 * 7);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->started = time();
  }
}
?>