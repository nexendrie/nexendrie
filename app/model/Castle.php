<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Castle as CastleEntity;

/**
 * Castle Model
 *
 * @author Jakub Konečný
 */
class Castle extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all castles
   * 
   * @return CastleEntity[]
   */
  function listOfCastles() {
    return $this->orm->castles->findAll();
  }
  
  /**
   * Get details of specified castle
   * 
   * @param int $id
   * @return CastleEntity
   * @throws CastleNotFoundException
   */
  function getCastle($id) {
    $castle = $this->orm->castles->getById($id);
    if(!$castle) throw new CastleNotFoundException;
    else return $castle;
  }
}

class CastleNotFoundException extends RecordNotFoundException {
  
}
?>