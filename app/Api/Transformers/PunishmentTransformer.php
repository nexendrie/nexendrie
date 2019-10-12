<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class PunishmentTransformer extends BaseTransformer {
  protected $fields = ["id", "user", "crime", "createdAt", "releasedAt", "numberOfShifts", "count", ];
  protected $fieldsRename = ["createdAt" => "created", "releasedAt" => "released", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Punishment::class;
  }
}
?>