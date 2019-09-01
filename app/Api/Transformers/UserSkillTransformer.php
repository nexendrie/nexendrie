<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class UserSkillTransformer extends BaseTransformer {
  protected $fields = ["level", "skill", ];
  protected $createSelfLink = false;

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\UserSkill::class;
  }

  public function getCollectionName(): string {
    return "skills";
  }
}
?>