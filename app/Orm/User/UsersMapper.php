<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class UsersMapper extends \Nextras\Orm\Mapper\Mapper {
  public function findByLikeName(string $publicname): ICollection {
    return $this->toCollection($this->builder()->where("publicname LIKE '%%$publicname%%'"));
  }
}
?>