<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class HouseTransformer extends BaseTransformer {
  protected $fields = ["id", "owner", "luxuryLevel", "breweryLevel", "hp", ];
  protected $fieldsRename = [];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\House::class;
  }
}
?>