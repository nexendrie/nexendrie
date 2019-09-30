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
      if($record === null) {
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
    $record = $this->orm->jobs->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>