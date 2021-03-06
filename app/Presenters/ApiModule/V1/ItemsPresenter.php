<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * ItemsPresenter
 *
 * @author Jakub Konečný
 */
final class ItemsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["shops"])) {
      $shop = (int) $this->params["associations"]["shops"];
      $record = $this->orm->shops->getById($shop);
      if($record === null) {
        $this->resourceNotFound("shop", $shop);
      }
      $records = $record->items;
    } elseif(isset($this->params["associations"]["item-sets"])) {
      $itemSet = (int) $this->params["associations"]["item-sets"];
      $record = $this->orm->itemSets->getById($itemSet);
      if($record === null) {
        $this->resourceNotFound("item set", $itemSet);
      }
      $records = [$record->weapon, $record->armor, $record->helmet, ];
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->items->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->items->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>