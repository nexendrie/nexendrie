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
  
  /**
   * Get specified user's monastary
   * 
   * @param int $id
   * @return MonasteryEntity
   * @throws UserNotFoundException
   * @throws NotInMonasteryException
   */
  function getByUser($id = 0) {
    if($id === 0) $id = $this->user->id;
    $user = $this->orm->users->getById($id);
    if(!$user) throw new UserNotFoundException;
    elseif(!$user->monastery) throw new NotInMonasteryException;
    else return $user->monastery;
  }
}

class MonasteryNotFoundException extends RecordNotFoundException {
  
}

class NotInMonasteryException extends AccessDeniedException {
  
}
?>