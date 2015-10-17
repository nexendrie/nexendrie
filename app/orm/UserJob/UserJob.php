<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * UserJob
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$jobs}
 * @property Job $job {m:1 Job::$userJobs}
 * @property int $started
 * @property bool $finished {default 0}
 * @property int|NULL $lastAction {default NULL}
 * @property int $count {default 0}
 * @property int $earned {default 0}
 * @property int $extra {default 0}
 * @property-read int $finishTime {virtual}
 */
class UserJob extends Entity {
  protected function getterFinishTime() {
    return $this->started + (60 * 60 * 24 * 7);
  }
}
?>