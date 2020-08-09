<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class JobTransformer extends BaseTransformer {
  protected array $fields = ["id", "name", "description", "help", "count", "award", "shift", "level", "neededSkill", "neededSkillLevel", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Job::class;
  }
}
?>