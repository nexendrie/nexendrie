<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\EventNotFoundException;

/**
 * Presenter Event
 *
 * @author Jakub Konečný
 */
class EventPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Events */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Events $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderView(int $id): void {
    try {
      $this->template->event = $event = $this->model->getEvent($id);
      $time = time();
      $status = "past";
      if($event->start <= $time AND $event->end >= $time) {
        $status = "active";
      } elseif($event->start > $time) {
        $status = "future";
      }
      $this->template->status = $status;
    } catch(EventNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
}
?>