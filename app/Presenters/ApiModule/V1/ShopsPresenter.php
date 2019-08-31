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
    $record = $this->orm->shops->getById($this->getId());
    $this->sendEntity($record);
  }

  protected function getEntityLinks(): array {
    $links = parent::getEntityLinks();
    $links["items"] = $this->createEntityLink("items");
    return $links;
  }
}
?>