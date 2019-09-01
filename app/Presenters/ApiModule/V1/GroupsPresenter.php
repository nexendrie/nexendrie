<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * GroupsPresenter
 *
 * @author Jakub Konečný
 */
final class GroupsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->groups->findAll();
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->groups->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>