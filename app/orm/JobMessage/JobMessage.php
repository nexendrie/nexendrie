<?php
namespace Nexendrie\Orm;

/**
 * JobMessage
 *
 * @author Jakub Konečný
 * @property Job $job {m:1 Job::$messages}
 * @property bool $success
 * @property string $message
 */
class JobMessage extends \Nextras\Orm\Entity\Entity {
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