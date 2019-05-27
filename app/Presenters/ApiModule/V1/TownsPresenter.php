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
      if(is_null($record)) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->ownedTowns;
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->towns->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $record = $this->orm->towns->getById($id);
    $this->sendEntity($record);
  }
}
?>