<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * HousesPresenter
 *
 * @author Jakub Konečný
 */
final class HousesPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      if($this->orm->users->getById($user) === null) {
        $this->resourceNotFound("user", $user);
      }
      $records = $this->orm->houses->findBy(["owner" => $user])->limitBy(1);
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->houses->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->houses->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>