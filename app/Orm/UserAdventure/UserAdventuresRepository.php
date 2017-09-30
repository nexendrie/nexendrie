<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class UserAdventuresRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [UserAdventure::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?UserAdventure {
    return $this->getBy(["id" => $id]);
  }
  
  
  /**
   * @param User|int $user
   * @return ICollection|UserAdventure[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  /**
   * Find specified user's active adventure
   * 
   * @param int $user User's id
   */
  public function getUserActiveAdventure(int $user): ?UserAdventure {
    return $this->getBy(["user" => $user, "progress<" => 10]);
  }
  
  /**
   * @param User|int $user
   */
  public function getLastAdventure($user): ?UserAdventure {
    return $this->findBy(["user" => $user])
      ->orderBy("started", ICollection::DESC)
      ->fetch();
  }
  
  /**
   * Get specified user's adventures from month
   *
   * @return ICollection|UserAdventure[]
   */
  public function findFromMonth(int $user, int $month = NULL, int $year = NULL): ICollection {
    $startOfMonthTS = mktime(0, 0, 0, $month ?? (int) date("n"), 1, $year ?? (int) date("Y"));
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["user" => $user, "started>" => $start, "started<" => $end]);
  }
  
  /**
   * Get open adventures
   * 
   * @return ICollection|UserAdventure[]
   */
  public function findOpenAdventures(): ICollection {
    $day = date("j");
    $month = date("n");
    $ts = mktime(0, 0, 0, (int) $month, (int) $day);
    return $this->findBy(["started<" => $ts, "progress<" => 10]);
  }
}
?>