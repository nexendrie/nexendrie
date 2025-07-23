<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserExpense|null getById(int $id)
 * @method UserExpense|null getBy(array $conds)
 * @method ICollection|UserExpense[] findBy(array $conds)
 * @method ICollection|UserExpense[] findAll()
 */
final class UserExpensesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [UserExpense::class];
  }
  
  /**
   * @return ICollection|UserExpense[]
   */
  public function findByUser(User|int $user): ICollection {
    return $this->findBy(["user" => $user,]);
  }
  
  /**
   * @return ICollection|UserExpense[]
   */
  public function findPaidThisMonth(User|int $user, string $category): ICollection {
    $month = date("n");
    $year = date("Y");
    $startOfMonthTS = (int) mktime(0, 0, 0, (int) $month, 1, (int) $year);
    $date = new \DateTime();
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy([
      "user" => $user, "category" => $category, "created>" => $start, "created<" => $end,
    ]);
  }
}
?>