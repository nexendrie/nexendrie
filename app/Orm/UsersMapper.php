<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class UsersMapper extends \Nextras\Orm\Mapper\Dbal\DbalMapper {
  public function findByLikeName(string $publicname): ICollection {
    // @phpstan-ignore argument.type
    return $this->toCollection($this->builder()->where("publicname LIKE '%%$publicname%%'"));
  }
}
?>