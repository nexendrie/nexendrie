<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class CastleTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "foundedAt", "owner", "level", "hp", ];
  protected $fieldsRename = ["foundedAt" => "founded", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Castle::class;
  }
}
?>