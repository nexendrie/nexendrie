<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * GuildRanksPresenter
 *
 * @author Jakub Konečný
 */
final class GuildRanksPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->guildRanks->findAll();
    $this->sendCollection($records);
  }

  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $record = $this->orm->guildRanks->getById($id);
    $this->sendEntity($record, null, "Guild rank");
  }
}
?>