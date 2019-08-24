<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * JobsPresenter
 *
 * @author Jakub Konečný
 */
final class JobsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["skills"])) {
      $skill = (int) $this->params["associations"]["skills"];
      $record = $this->orm->skills->getById($skill);
      if(is_null($record)) {
        $this->resourceNotFound("skill", $skill);
      }
      $records = $record->jobs;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->jobs->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $record = $this->orm->jobs->getById($id);
    $this->sendEntity($record);
  }
}
?>