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
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->guildRanks->findAll();
    $this->sendCollection($records);
  }

  protected function getInvalidEntityName(): string {
    return "Guild rank";
  }

  public function actionRead(): void {
    $record = $this->orm->guildRanks->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>