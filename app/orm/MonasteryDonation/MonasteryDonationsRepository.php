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
  
}
?>