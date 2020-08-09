<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class AdventureNpcTransformer extends BaseTransformer {
  protected array $fields = ["id", "name", "adventure", "order", "hitpoints", "strength", "armor", "initiative", "reward", "encounterText", "victoryText",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\AdventureNpc::class;
  }

  public function getCollectionName(): string {
    return "npcs";
  }
}
?>