<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * CastlesPresenter
 *
 * @author Jakub Konečný
 */
final class CastlesPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->castles->findAll();
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->castles->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>