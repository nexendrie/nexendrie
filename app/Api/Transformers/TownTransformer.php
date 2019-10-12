<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class TownTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "createdAt", "owner", "price", "onMarket", "denizens", ];
  protected $fieldsRename = ["createdAt" => "created", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Town::class;
  }
}
?>