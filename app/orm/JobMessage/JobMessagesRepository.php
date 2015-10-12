<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
 Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method JobMessage|NULL getById($id)
 * @method ICollection|JobMessage[] findByJobAndSuccess($job,$success)
 */
class JobMessagesRepository extends Repository {
  
}
?>