<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class ItemsMapper extends \Nextras\Orm\Mapper\Mapper {
  static function getEntityClassNames() {
    return [Item::class];
  }
}
?>