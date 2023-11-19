<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Nexendrie\EventCalendar\Simple\EventCalendar as Calendar;
use Nexendrie\Model\ICalendarResponse;

/**
 * Presenter Chronicle
 *
 * @author Jakub Konečný
 */
final class ChroniclePresenter extends BasePresenter {
  protected \Nexendrie\Model\Chronicle $model;
  protected \Nexendrie\Model\Events $eventsModel;
  protected \Nexendrie\Model\Marriage $marriagesModel;
  
  public function __construct(\Nexendrie\Model\Chronicle $model, \Nexendrie\Model\Events $eventsModel, \Nexendrie\Model\Marriage $marriagesModel) {
    parent::__construct();
    $this->model = $model;
    $this->eventsModel = $eventsModel;
    $this->marriagesModel = $marriagesModel;
  }
  
  public function renderDefault(): void {
    $this->template->articles = $this->model->articles();
  }
  
  public function renderCrimes(string $user = ""): void {
    $this->template->crimes = $this->model->crimes($user);
  }
  
  public function renderMarriages(): void {
    $this->template->marriages = $this->marriagesModel->listOfMarriages();
  }

  public function renderCalendar(): void {
    $componentFactory = new CalendarFactory();
    $calendarComponent = $componentFactory->createCalendar($this->eventsModel->getCalendar());
    $this->sendResponse(new ICalendarResponse((string) $calendarComponent));
  }
  
  protected function createComponentEventsCalendar(): Calendar {
    $calendar = new Calendar();
    $calendar->firstDay = Calendar::FIRST_MONDAY;
    $calendar->events = $this->eventsModel;
    $calendar->onDateChange[] = [$this->eventsModel, "loadEvents"];
    $calendar->options[Calendar::OPT_WDAY_MAX_LEN] = PHP_INT_MAX;
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
      foreach($this->template->crimes as $crime) {
        $time = max($time, $crime->updated);
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