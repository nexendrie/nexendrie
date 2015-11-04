<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Monastery as MonasteryEntity;

/**
 * Monastery Model
 *
 * @author Jakub Konečný
 */
class Monastery extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all monasteries
   * 
   * @return MonasteryEntity[]
   */
  function listOfMonasteries() {
    return $this->orm->monasterires->findAll();
  }
  
  /**
   * Get specified monastery
   * 
   * @param int $id
   * @return MonasteryEntity
   * @throws MonasteryNotFoundException
   */
  function get($id) {
    $monastery = $this->orm->monasterires->getById($id);
    if(!$monastery) throw new MonasteryNotFoundException;
    else return $monastery;
  }
}

class MonasteryNotFoundException extends RecordNotFoundException {
  
}
?>