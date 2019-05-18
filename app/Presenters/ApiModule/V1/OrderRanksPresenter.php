<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

final class OrderRanksPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->orderRanks->findAll();
    $this->sendCollection($records, "orderRanks");
  }

  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $group = $this->orm->orderRanks->getById($id);
    $this->sendEntity($group, "orderRank", "order rank");
  }
}
?>