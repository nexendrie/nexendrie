<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * UsersPresenter
 *
 * @author Jakub Konečný
 */
final class UsersPresenter extends BasePresenter {
  /** @var bool */
  protected $cachingEnabled = false;

  public function actionReadAll(): void {
    if(isset($this->params["associations"]["towns"])) {
      $town = (int) $this->params["associations"]["towns"];
      $record = $this->orm->towns->getById($town);
      if($record === null) {
        $this->resourceNotFound("town", $town);
      }
      $records = $record->denizens;
    } elseif(isset($this->params["associations"]["monasteries"])) {
      $monastery = (int) $this->params["associations"]["monasteries"];
      $record = $this->orm->monasteries->getById($monastery);
      if($record === null) {
        $this->resourceNotFound("monastery", $monastery);
      }
      $records = $record->members;
    } elseif(isset($this->params["associations"]["guilds"])) {
      $guild = (int) $this->params["associations"]["guilds"];
      $record = $this->orm->guilds->getById($guild);
      if($record === null) {
        $this->resourceNotFound("guild", $guild);
      }
      $records = $record->members;
    } elseif(isset($this->params["associations"]["orders"])) {
      $order = (int) $this->params["associations"]["orders"];
      $record = $this->orm->orders->getById($order);
      if($record === null) {
        $this->resourceNotFound("order", $order);
      }
      $records = $record->members;
    } elseif(isset($this->params["associations"]["groups"])) {
      $group = (int) $this->params["associations"]["groups"];
      $record = $this->orm->groups->getById($group);
      if($record === null) {
        $this->resourceNotFound("group", $group);
      }
      $records = $record->members;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->users->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->users->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>