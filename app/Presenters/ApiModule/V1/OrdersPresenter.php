<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * OrdersPresenter
 *
 * @author Jakub Konečný
 */
final class OrdersPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->orders->findAll();
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->orders->getById($this->getId());
    $this->sendEntity($record);
  }

  protected function getEntityLinks(): array {
    $links = parent::getEntityLinks();
    $links["users"] = $this->createEntityLink("users");
    return $links;
  }
}
?>