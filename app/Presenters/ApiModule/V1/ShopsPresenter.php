<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * ShopsPresenter
 *
 * @author Jakub Konečný
 */
final class ShopsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->shops->findAll();
    $this->sendCollection($records, "shops");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $shop = $this->orm->shops->getById($id);
    $this->sendEntity($shop, "shop");
  }
}
?>