<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * AdventuresPresenter
 *
 * @author Jakub Konečný
 */
final class AdventuresPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["events"])) {
      $event = (int) $this->params["associations"]["events"];
      $record = $this->orm->events->getById($event);
      if($record === null) {
        $this->resourceNotFound("event", $event);
      }
      $records = $record->adventures;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->adventures->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->adventures->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>