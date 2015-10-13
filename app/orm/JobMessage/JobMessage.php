<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * JobMessage
 *
 * @author Jakub Konečný
 * @property Job $job {m:1 Job::$messages}
 * @property bool $success
 * @property string $message
 */
class JobMessage extends Entity {
  /**
   * @return \Nexendrie\Orm\JobMessageDummy
   */
  function dummy() {
    return new JobMessageDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>