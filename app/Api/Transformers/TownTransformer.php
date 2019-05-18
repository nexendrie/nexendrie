<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class TownTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "foundedAt", "owner", "price", "onMarket", "denizens",];
  protected $fieldsRename = ["foundedAt" => "founded",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Town::class;
  }
}
?>