<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * UsersPresenter
 *
 * @author Jakub Konečný
 */
final class UsersPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["towns"])) {
      $town = (int) $this->params["associations"]["towns"];
      $record = $this->orm->towns->getById($town);
      if(is_null($record)) {
        $this->resourceNotFound("town", $town);
      }
      $records = $record->denizens;
    } elseif(isset($this->params["associations"]["monasteries"])) {
      $monastery = (int) $this->params["associations"]["monasteries"];
      $record = $this->orm->monasteries->getById($monastery);
      if(is_null($record)) {
        $this->resourceNotFound("monastery", $monastery);
      }
      $records = $record->members;
    } elseif(isset($this->params["associations"]["guilds"])) {
      $guild = (int) $this->params["associations"]["guilds"];
      $record = $this->orm->guilds->getById($guild);
      if(is_null($record)) {
        $this->resourceNotFound("guild", $guild);
      }
      $records = $record->members;
    } elseif(isset($this->params["associations"]["orders"])) {
      $order = (int) $this->params["associations"]["orders"];
      $record = $this->orm->orders->getById($order);
      if(is_null($record)) {
        $this->resourceNotFound("order", $order);
      }
      $records = $record->members;
    } elseif(isset($this->params["associations"]["groups"])) {
      $group = (int) $this->params["associations"]["groups"];
      $record = $this->orm->groups->getById($group);
      if(is_null($record)) {
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

  protected function getEntityLinks(): array {
    $links = parent::getEntityLinks();
    $links["articles"] = $this->createEntityLink("articles");
    $links["comments"] = $this->createEntityLink("comments");
    $links["mounts"] = $this->createEntityLink("mounts");
    $links["polls"] = $this->createEntityLink("polls");
    $links["punishments"] = $this->createEntityLink("punishments");
    $links["skills"] = $this->createEntityLink("skills");
    $links["towns"] = $this->createEntityLink("towns");
    return $links;
  }
}
?>