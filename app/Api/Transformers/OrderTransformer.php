<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class OrderTransformer extends BaseTransformer {
  protected array $fields = ["id", "name", "description", "level", "createdAt", "members", ];
  protected array $fieldsRename = ["createdAt" => "created", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Order::class;
  }
}
?>