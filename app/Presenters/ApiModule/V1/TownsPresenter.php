<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * TownsPresenter
 *
 * @author Jakub Konečný
 */
final class TownsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      $record = $this->orm->users->getById($user);
      if($record === null) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->ownedTowns;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->towns->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->towns->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>