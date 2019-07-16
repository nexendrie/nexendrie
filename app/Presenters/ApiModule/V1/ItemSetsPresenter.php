<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * ItemSetsPresenter
 *
 * @author Jakub Konečný
 */
final class ItemSetsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->itemSets->findAll();
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $record = $this->orm->itemSets->getById($id);
    $this->sendEntity($record, null, "Item set");
  }
}
?>