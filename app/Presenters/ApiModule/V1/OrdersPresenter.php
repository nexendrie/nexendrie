<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * OrdersPresenter
 *
 * @author Jakub Konečný
 */
final class OrdersPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->orders->findAll();
    $this->sendCollection($records, "orders");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $castle = $this->orm->orders->getById($id);
    $this->sendEntity($castle, "order");
  }
}
?>