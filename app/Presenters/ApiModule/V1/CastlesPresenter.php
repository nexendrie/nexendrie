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
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->castles->findAll();
    $this->sendCollection($records, "castles");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $castle = $this->orm->castles->getById($id);
    $this->sendEntity($castle, "castle");
  }
}
?>