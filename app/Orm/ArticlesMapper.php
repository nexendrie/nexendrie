<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class ArticlesMapper extends \Nextras\Orm\Mapper\Mapper {
  public function findByText(string $text): ICollection {
    return $this->toCollection($this->builder()->where("text LIKE '%%$text%%'")->orWhere("title LIKE '%%$text%%'"));
  }
}
?>