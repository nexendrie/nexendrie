<?php
namespace Nexendrie\Orm;

/**
 * ElectionResult
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $candidate {m:1 User::$elections}
 * @property Town $town {m:1 Town::$electionResults}
 * @property int $votes
 * @property bool $elected {default false}
 * @property int $year
 * @property int $month
 */
class ElectionResult extends \Nextras\Orm\Entity\Entity {
  
}
?>