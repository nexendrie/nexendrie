<?php
namespace Nexendrie\Orm;

/**
 * JobMessageDummy
 *
 * @author Jakub Konečný
 * @property int $success
 * @property string $message
 */
class JobMessageDummy extends DummyEntity {
  /** @var bool */
  protected $success;
  /** @var string */
  protected $message;
  
  function __construct(JobMessage $message) {
    $this->success = $message->success;
    $this->message = $message->message;
  }
}
?>