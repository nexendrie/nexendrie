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
 * @property int $finished {default 0}
 * @property int|NULL $lastAction {default NULL}
 * @property int $count {default 0}
 */
class UserJob extends Entity {
  
}
?>