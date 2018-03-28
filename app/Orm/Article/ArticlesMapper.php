<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class ArticlesMapper extends \Nextras\Orm\Mapper\Mapper {
  public function findByLikeTitle(string $title): ICollection {
    return $this->toCollection($this->builder()->where("title LIKE '%%$title%%'"));
  }
  
  public function findByText(string $text): ICollection {
    return $this->toCollection($this->builder()->where("text LIKE '%%$text%%'"));
  }
}
?>