<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Message|null getById(int $id)
 * @method Message|null getBy(array $conds)
 * @method ICollection|Message[] findBy(array $conds)
 * @method ICollection|Message[] findAll()
 */
final class MessagesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Message::class];
  }
  
  /**
   * @return ICollection|Message[]
   */
  public function findByFrom(User|int $from): ICollection {
    return $this->findBy(["from" => $from]);
  }
  
  /**
   * @return ICollection|Message[]
   */
  public function findByTo(User|int $to): ICollection {
    return $this->findBy(["to" => $to]);
  }

  /**
   * @return ICollection|Message[]
   */
  public function findUnnotified(User|int $to): ICollection {
    return $this->findBy([
      "to" => $to,
      "notified" => false,
    ]);
  }
}
?>