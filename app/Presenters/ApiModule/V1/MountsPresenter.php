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
      if(is_null($record)) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->mounts;
    } elseif(isset($this->params["associations"]["mount-types"])) {
      $mountType = (int) $this->params["associations"]["mount-types"];
      $record = $this->orm->mountTypes->getById($mountType);
      if(is_null($record)) {
        $this->resourceNotFound("mount type", $mountType);
      }
      $records = $record->mounts;
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->mounts->findAll();
    }
    $this->sendCollection($records, "mounts");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $mount = $this->orm->mounts->getById($id);
    $this->sendEntity($mount, "mount");
  }
}
?>