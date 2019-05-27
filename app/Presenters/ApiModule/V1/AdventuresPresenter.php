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
      if(is_null($record)) {
        $this->resourceNotFound("event", $event);
      }
      $records = $record->adventures;
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->adventures->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $record = $this->orm->adventures->getById($id);
    $this->sendEntity($record);
  }
}
?>