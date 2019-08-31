<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * MealsPresenter
 *
 * @author Jakub Konečný
 */
final class MealsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->meals->findAll();
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->meals->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>