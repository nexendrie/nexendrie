<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * GuildsPresenter
 *
 * @author Jakub Konečný
 */
final class GuildsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["towns"])) {
      $town = (int) $this->params["associations"]["towns"];
      $record = $this->orm->towns->getById($town);
      if(is_null($record)) {
        $this->resourceNotFound("town", $town);
      }
      $records = $record->guilds;
    } elseif(isset($this->params["associations"]["skills"])) {
      $skill = (int) $this->params["associations"]["skills"];
      $record = $this->orm->skills->getById($skill);
      if(is_null($record)) {
        $this->resourceNotFound("skill", $skill);
      }
      $records = $record->guilds;
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->guilds->findAll();
    }
    $this->sendCollection($records, "guilds");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $guild = $this->orm->guilds->getById($id);
    $this->sendEntity($guild, "guild");
  }
}
?>