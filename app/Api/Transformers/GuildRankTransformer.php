<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class GuildRankTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "incomeBonus", "guildFee",];
  protected $fieldsRename = ["guildFee" => "fee",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\GuildRank::class;
  }
}
?>