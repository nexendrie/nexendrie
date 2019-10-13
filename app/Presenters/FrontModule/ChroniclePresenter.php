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
    $calendar = new class extends Calendar {
      public function render(): void {
        $this->onDateChange($this->year, $this->month);
        parent::render();
      }
    };
    $calendar->language = Calendar::LANG_CZ;
    $calendar->firstDay = Calendar::FIRST_MONDAY;
    $calendar->options = [
      Calendar::OPT_BOTTOM_NAV_PREV => "Předchozí měsíc",
      Calendar::OPT_BOTTOM_NAV_NEXT => "Následující měsíc",
    ];
    $calendar->events = $this->eventsModel;
    $calendar->onDateChange[] = [$this->eventsModel, "loadEvents"];
    return $calendar;
  }

  protected function getDataModifiedTime(): int {
    $time = 0;
    if(isset($this->template->articles)) {
      /** @var \Nexendrie\Orm\Article $article */
      foreach($this->template->articles as $article) {
        $time = max($time, $article->updated);
      }
      return $time;
    }
    if(isset($this->template->crimes)) {
      /** @var \Nexendrie\Orm\Punishment $crime */
      foreach($this->template->crimes as $marriage) {
        $time = max($time, $marriage->updated);
      }
      return $time;
    }
    if(isset($this->template->marriages)) {
      /** @var \Nexendrie\Orm\Marriage $marriage */
      foreach($this->template->marriages as $marriage) {
        $time = max($time, $marriage->updated);
      }
      return $time;
    }
    return time();
  }
}
?>