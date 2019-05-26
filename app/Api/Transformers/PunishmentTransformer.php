<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class PunishmentTransformer extends BaseTransformer {
  protected $fields = ["id", "user", "crime", "imprisonedAt", "releasedAt", "numberOfShifts", "count",];
  protected $fieldsRename = ["imprisonedAt" => "imprisoned", "releasedAt" => "released",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Punishment::class;
  }
}
?>