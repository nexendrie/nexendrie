<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class ShopTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "items", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Shop::class;
  }
}
?>