<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class AdventureTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "intro", "epilogue", "reward", "level", "event", "npcs",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Adventure::class;
  }
}
?>