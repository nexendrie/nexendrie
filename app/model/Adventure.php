<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Adventure as AdventureEntity;

/**
 * Adventure Model
 *
 * @author Jakub Konečný
 */
class Adventure extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all adventures
   * 
   * @return AdventureEntity[]
   */
  function listOfAdventures() {
    return $this->orm->adventures->findAll();
  }
  
  /**
   * Get specified adventure
   *  
   * @param int $id
   * @return AdventureEntity
   * @throws AdventureNotFoundException
   */
  function get($id) {
    $adventure = $this->orm->adventures->getById($id);
    if(!$adventure) throw new AdventureNotFoundException;
    else return $adventure;
  }
  
  /**
   * Add new adventure
   * 
   * @param array $data
   * @return void
   */
  function addAdventure(array $data) {
    $adventure = new AdventureEntity;
    foreach($data as $key => $value) {
      $adventure->$key = $value;
    }
    $this->orm->adventures->persistAndFlush($adventure);
  }
  
  /**
   * Edit adventure
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws \Nexendrie\Model\AdventureNotFoundException
   */
  function editAdventure($id, array $data) {
    try {
      $adventure = $this->orm->adventures->getById($id);
    } catch(AdventureNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $adventure->$key = $value;
    }
    $this->orm->adventures->persistAndFlush($adventure);
  }
}

class AdventureNotFoundException extends RecordNotFoundException {
  
}
?>