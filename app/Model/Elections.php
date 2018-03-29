<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\User as UserEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Elections Model
 *
 * @author Jakub Konečný
 */
class Elections {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get number of councillors for the town
   */
  public function getNumberOfCouncillors(int $townId): int {
    $town = $this->orm->towns->getById($townId);
    if(is_null($town)) {
      return 0;
    }
    $denizens = $town->denizens->countStored();
    if($denizens <= 3) {
      return 0;
    }
    return max(1, (int) ($denizens / 5));
  }
  
  /**
   * Get candidates for elections
   *
   * @return UserEntity[]|ICollection
   */
  public function getCandidates(int $town): ICollection {
    return $this->orm->users->findTownCitizens($town);
  }
}
?>