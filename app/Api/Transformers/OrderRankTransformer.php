<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class OrderRankTransformer extends BaseTransformer {
  protected array $fields = ["id", "name", "adventureBonus", "orderFee", ];
  protected array $fieldsRename = ["orderFee" => "fee", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\OrderRank::class;
  }
}
?>