<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * MountTypesPresenter
 *
 * @author Jakub Konečný
 */
final class MountTypesPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->mountTypes->findAll();
    $this->sendCollection($records, "mountTypes");
  }

  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $mountType = $this->orm->mountTypes->getById($id);
    $this->sendEntity($mountType, "mountType", "mount type");
  }
}
?>