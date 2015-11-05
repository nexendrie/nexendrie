<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ICollection|MonasteryDonation[] findByUser($user)
 * @method ICollection|MonasteryDonation[] findByMonastery($monastery)
 */
class MonasteryDonationsRepository extends Repository {
  /**
   * Get donations made this month by specified user
   * 
   * @param int $user
   * @return ICollection|MonasteryDonation[]
   */
  function findDonatedThisMonth($user) {
    $month = date("n");
    $year = date("Y");
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(array("user" => $user, "when>" => $start, "when<" => $end));
  }
}
?>