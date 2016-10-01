<?php
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
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get number of councillors for the town
   * 
   * @param int $town
   * @return int
   */
  function getNumberOfCouncillors($town) {
    /** @var int */
    $denizens = $this->orm->towns->getById($town)->denizens->countStored();
    if($denizens <= 3) return 0;
    elseif($denizens <= 6) return 1;
    else return (int) ($denizens / 5);
  }
  
  /**
   * Get candidates for elections
   * 
   * @param int $town
   * @return UserEntity[]|ICollection
   */
  function getCandidates($town) {
    return $this->orm->users->findTownCitizens($town);
  }
}
?>