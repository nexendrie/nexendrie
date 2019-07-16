<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class OrderTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "level", "foundedAt", "members", ];
  protected $fieldsRename = ["foundedAt" => "founded", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Order::class;
  }
}
?>