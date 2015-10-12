<?php
namespace Nexendrie\Orm;

/**
 * JobMessageDummy
 *
 * @author Jakub Konečný
 * @property int $success
 * @property string $message
 */
class JobMessageDummy extends \Nette\Object {
  /** @var bool */
  protected $success;
  /** @var string */
  protected $message;
  
  function __construct(JobMessage $message) {
    $this->success = (bool) $message->success;
    $this->message = $message->message;
  }
  
  /**
   * @return array
   */
  function toArray() {
    $return = array();
    foreach($this as $key => $value) {
      $return[$key] = $value;
    }
    return $return;
  }
}
?>