<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\EventNotFoundException;

/**
 * Presenter Event
 *
 * @author Jakub Konečný
 */
class EventPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Events @autowire */
  protected $model;
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function renderView($id) {
    try {
      $this->template->event = $event = $this->model->getEvent($id);
      $time = time();
      if($event->start <= $time AND $event->end >= $time) $status = "active";
      elseif($event->start > $time) $status = "future";
      else $status = "past";
      $this->template->status = $status;
    } catch(EventNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>