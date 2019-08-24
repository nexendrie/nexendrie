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
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->shops->findAll();
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $record = $this->orm->shops->getById($id);
    $this->sendEntity($record);
  }
}
?>