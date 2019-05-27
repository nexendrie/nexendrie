<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * NpcsPresenter
 *
 * @author Jakub Konečný
 */
final class NpcsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["adventures"])) {
      $adventure = (int) $this->params["associations"]["adventures"];
      $record = $this->orm->adventures->getById($adventure);
      if(is_null($record)) {
        $this->resourceNotFound("adventure", $adventure);
      }
      $records = $record->npcs;
      unset($record); // needed for PHPStan until another association is supported
    } else {
      return;
    }
    $this->sendCollection($records);
  }

  public function actionRead(): void {
    $id = (int) $this->params["id"];
    if(isset($this->params["associations"]["adventures"])) {
      $adventure = (int) $this->params["associations"]["adventures"];
      $record = $this->orm->adventureNpcs->getBy(["id" => $id, "adventure" => $adventure,]);
    } else {
      return;
    }
    $this->sendEntity($record);
  }
}
?>