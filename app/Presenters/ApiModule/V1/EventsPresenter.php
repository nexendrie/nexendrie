<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * EventsPresenter
 *
 * @author Jakub Konečný
 */
final class EventsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->events->findAll();
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $record = $this->orm->events->getById($id);
    $this->sendEntity($record);
  }
}
?>