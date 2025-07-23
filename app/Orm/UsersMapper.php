<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Mapper\Dbal\DbalMapper;

/**
 * @author Jakub Konečný
 * @extends DbalMapper<User>
 */
final class UsersMapper extends DbalMapper {
  public function findByLikeName(string $publicname): ICollection {
    // @phpstan-ignore argument.type
    return $this->toCollection($this->builder()->where("publicname LIKE '%%$publicname%%'"));
  }
}
?>