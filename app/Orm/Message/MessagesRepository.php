<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class MessagesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Message::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Message {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $from
   * @return ICollection|Message[]
   */
  public function findByFrom($from): ICollection {
    return $this->findBy(["from" => $from]);
  }
  
  /**
   * @param User|int $to
   * @return ICollection|Message[]
   */
  public function findByTo($to): ICollection {
    return $this->findBy(["to" => $to]);
  }
}
?>