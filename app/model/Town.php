<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Town as TownEntity;

/**
 * Town Model
 *
 * @author Jakub Konečný
 */
class Town extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get specified town
   * 
   * @param int $id Town's id
   * @return TownEntity
   * @throws TownNotFoundException
   */
  function get($id) {
    $town = $this->orm->towns->getById($id);
    if(!$town) throw new TownNotFoundException;
    else return $town;
  }
  
  /**
   * Get list of all towns
   * 
   * @return TownEntity[]
   */
  function listOfTowns() {
    return $this->orm->towns->findAll();
  }
  
  /**
   * Add new town
   * 
   * @param array $data
   * @return void
   */
  function add(array $data) {
    $town = new TownEntity;
    $this->orm->towns->attach($town);
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * Edit specified town
   * 
   * @param int $id Town's id
   * @param array $data
   * @return void
   * @throws TownNotFoundException
   */
  function edit($id, array $data) {
    try {
      $town = $this->get($id);
    } catch(TownNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
}

class TownNotFoundException extends RecordNotFoundException {
  
}
?>