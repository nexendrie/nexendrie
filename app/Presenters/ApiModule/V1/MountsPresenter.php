<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * HousesPresenter
 *
 * @author Jakub Konečný
 */
final class MountsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      $record = $this->orm->users->getById($user);
      if($record === null) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->mounts;
    } elseif(isset($this->params["associations"]["mount-types"])) {
      $mountType = (int) $this->params["associations"]["mount-types"];
      $record = $this->orm->mountTypes->getById($mountType);
      if($record === null) {
        $this->resourceNotFound("mount type", $mountType);
      }
      $records = $record->mounts;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->mounts->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->mounts->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>