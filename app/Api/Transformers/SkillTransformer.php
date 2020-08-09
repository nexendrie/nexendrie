<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class SkillTransformer extends BaseTransformer {
  protected array $fields = ["id", "name", "price", "maxLevel", "type", "stat", "statIncrease", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Skill::class;
  }
}
?>