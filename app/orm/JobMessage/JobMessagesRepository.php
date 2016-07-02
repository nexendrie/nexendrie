<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method JobMessage|NULL getById($id)
 * @method ICollection|JobMessage[] findByJobAndSuccess($job,$success)
 */
class JobMessagesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [JobMessage::class];
  }
}
?>