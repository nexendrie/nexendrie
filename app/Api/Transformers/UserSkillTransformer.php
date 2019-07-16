<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class UserSkillTransformer extends BaseTransformer {
  protected $fields = ["level", "skill", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\UserSkill::class;
  }
}
?>