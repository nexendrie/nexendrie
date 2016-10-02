<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class MessagesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Message::class];
  }
  
  /**
   * @param int $id
   * @return Message|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $from
   * @return ICollection|Message[]
   */
  function findByFrom($from) {
    return $this->findBy(["from" => $from]);
  }
  
  /**
   * @param User|int $to
   * @return ICollection|Message[]
   */
  function findByTo($to) {
    return $this->findBy(["to" => $to]);
  }
}
?>