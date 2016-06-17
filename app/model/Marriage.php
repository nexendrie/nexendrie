<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Marrriage as MarriageEntity;

/**
 * Marriage Model
 *
 * @author Jakub Konečný
 */
class Marriage extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all marriages
   * 
   * @return MarriageEntity[]
   */
  function listOfMarriages() {
    return $this->orm->marriages->findAll();
  }
}
?>