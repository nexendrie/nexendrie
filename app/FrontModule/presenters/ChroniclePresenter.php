<?php
namespace Nexendrie\Presenters\FrontModule;

use EventCalendar\Simple\SimpleCalendar as Calendar;

/**
 * Presenter Chronicle
 *
 * @author Jakub Konečný
 */
class ChroniclePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Chronicle @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Events @autowire */
  protected $eventsModel;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->articles = $this->model->articles();
  }
  
  /**
   * @return void
   */
  function renderCrimes() {
    $this->template->crimes = $this->model->crimes();
  }
  
  /**
   * @return Calendar
   */
  protected function createComponentEventsCalendar() {
    $calendar = new Calendar;
    $calendar->language = Calendar::LANG_CZ;
    $calendar->firstDay = Calendar::FIRST_MONDAY;
    $calendar->options = array(
      Calendar::OPT_BOTTOM_NAV_PREV => "Předchozí měsíc",
      Calendar::OPT_BOTTOM_NAV_NEXT => "Následující měsíc"
    );
    $this->eventsModel->loadEvents();
    $calendar->events = $this->eventsModel;
    $calendar->onDateChange[] = array($this->eventsModel, "loadEvents");
    return $calendar;
  }
}
?>