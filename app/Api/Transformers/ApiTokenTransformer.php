<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

class ApiTokenTransformer extends BaseTransformer {
  protected array $fields = ["id", "token", "expireAt", "createdAt", "user",];
  protected array $fieldsRename = ["createdAt" => "created", "expireAt" => "expire",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\ApiToken::class;
  }

  public function getCollectionName(): string {
    return "tokens";
  }
}
?>