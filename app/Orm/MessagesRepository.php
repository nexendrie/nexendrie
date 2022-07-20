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

  /**
   * @param User|int $to
   * @return ICollection|Message[]
   */
  public function findUnnotified($to): ICollection {
    return $this->findBy([
      "to" => $to,
      "notified" => false,
    ]);
  }
}
?>