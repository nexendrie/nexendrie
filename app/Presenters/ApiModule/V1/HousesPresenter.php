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
      if(is_null($this->orm->users->getById($user))) {
        $this->resourceNotFound("user", $user);
      }
      $records = $this->orm->houses->findBy(["owner" => $user])->limitBy(1);
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->houses->findAll();
    }
    $this->sendCollection($records, "houses");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $house = $this->orm->houses->getById($id);
    $this->sendEntity($house, "house");
  }
}
?>