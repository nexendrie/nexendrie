<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * MonasteriesPresenter
 *
 * @author Jakub Konečný
 */
final class MonasteriesPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["towns"])) {
      $town = (int) $this->params["associations"]["towns"];
      $record = $this->orm->towns->getById($town);
      if(is_null($record)) {
        $this->resourceNotFound("town", $town);
      }
      $records = $record->monasteries;
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->monasteries->findAll();
    }
    $this->sendCollection($records, "monasteries");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $monastery = $this->orm->monasteries->getById($id);
    $this->sendEntity($monastery, "monastery");
  }
}
?>