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
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->groups->findAll();
    $this->sendCollection($records, "groups");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $group = $this->orm->groups->getById($id);
    $this->sendEntity($group, "group");
  }
}
?>