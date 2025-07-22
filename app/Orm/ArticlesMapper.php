<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class ArticlesMapper extends \Nextras\Orm\Mapper\Dbal\DbalMapper {
  public function findByText(string $text): ICollection {
    // @phpstan-ignore argument.type, argument.type
    return $this->toCollection($this->builder()->where("text LIKE '%%$text%%'")->orWhere("title LIKE '%%$text%%'"));
  }
}
?>