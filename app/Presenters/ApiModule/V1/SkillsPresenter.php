<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * SkillsPresenter
 *
 * @author Jakub Konečný
 */
final class SkillsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      $record = $this->orm->users->getById($user);
      if(is_null($record)) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->skills;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->skills->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->skills->getById($this->getId());
    $this->sendEntity($record);
  }

  protected function getEntityLinks(): array {
    $links = parent::getEntityLinks();
    $links["jobs"] = $this->createEntityLink("jobs");
    $links["guilds"] = $this->createEntityLink("guilds");
    return $links;
  }
}
?>