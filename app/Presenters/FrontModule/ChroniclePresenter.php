<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use EventCalendar\Simple\SimpleCalendar as Calendar;

/**
 * Presenter Chronicle
 *
 * @author Jakub Konečný
 */
final class ChroniclePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Chronicle */
  protected $model;
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  /** @var \Nexendrie\Model\Marriage */
  protected $marriagesModel;
  
  public function __construct(\Nexendrie\Model\Chronicle $model, \Nexendrie\Model\Events $eventsModel, \Nexendrie\Model\Marriage $marriagesModel) {
    parent::__construct();
    $this->model = $model;
    $this->eventsModel = $eventsModel;
    $this->marriagesModel = $marriagesModel;
  }
  
  public function renderDefault(): void {
    $this->template->articles = $this->model->articles();
  }
  
  public function renderCrimes(): void {
    $this->template->crimes = $this->model->crimes();
  }
  
  public function renderMarriages(): void {
    $this->template->marriages = $this->marriagesModel->listOfMarriages();
  }
  
  protected function createComponentEventsCalendar(): Calendar {
    $calendar = new Calendar();
    $calendar->language = Calendar::LANG_CZ;
    $calendar->firstDay = Calendar::FIRST_MONDAY;
    $calendar->options = [
      Calendar::OPT_BOTTOM_NAV_PREV => "Předchozí měsíc",
      Calendar::OPT_BOTTOM_NAV_NEXT => "Následující měsíc"
    ];
    $this->eventsModel->loadEvents();
    $calendar->events = $this->eventsModel;
    $calendar->onDateChange[] = [$this->eventsModel, "loadEvents"];
    return $calendar;
  }
}
?>