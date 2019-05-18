<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class OrderRankTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "adventureBonus", "orderFee",];
  protected $fieldsRename = ["orderFee" => "fee",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\OrderRank::class;
  }
}
?>