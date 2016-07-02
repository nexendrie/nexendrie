<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Message|NULL getById($id)
 * @method ICollection|Message[] findByFrom($from)
 * @method ICollection|Message[] findByTo($to)
 */
class MessagesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Message::class];
  }
}
?>