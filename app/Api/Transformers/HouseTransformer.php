<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class HouseTransformer extends BaseTransformer {
  protected array $fields = ["id", "owner", "luxuryLevel", "breweryLevel", "hp", ];
  protected array $fieldsRename = [];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\House::class;
  }
}
?>