<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * PollsPresenter
 *
 * @author Jakub Konečný
 */
final class PollsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      $record = $this->orm->users->getById($user);
      if($record === null) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->polls;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->polls->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->polls->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>