<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

final class GuildRanksPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->guildRanks->findAll();
    $this->sendCollection($records, "guildRanks");
  }

  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $group = $this->orm->guildRanks->getById($id);
    $this->sendEntity($group, "guildRank", "guild rank");
  }
}
?>