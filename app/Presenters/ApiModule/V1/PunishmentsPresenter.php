<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * PunishmentsPresenter
 *
 * @author Jakub Konečný
 */
final class PunishmentsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      $record = $this->orm->users->getById($user);
      if($record === null) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->punishments;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->punishments->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->punishments->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>