<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\ItemSet as ItemSetEntity;

/**
 * ItemSet Model
 *
 * @author Jakub Konečný
 */
class ItemSet extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get list of all item sets
   * 
   * @return ItemSetEntity[]
   */
  function listOfSets() {
    return $this->orm->itemSets->findAll();
  }
  
  /**
   * Get specified item set
   *  
   * @param int $id
   * @return AdventureEntity
   * @throws ItemSetNotFoundException
   */
  function get($id) {
    $set = $this->orm->itemSets->getById($id);
    if(!$set) throw new ItemSetNotFoundException;
    else return $set;
  }
  
  /**
   * Add new item set
   * 
   * @param array $data
   * @return void
   */
  function add(array $data) {
    $set = new ItemSetEntity;
    $this->orm->itemSets->attach($set);
    foreach($data as $key => $value) {
      $set->$key = $value;
    }
    $this->orm->itemSets->persistAndFlush($set);
  }
  
  /**
   * Edit specified item set
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws \Nexendrie\Model\ItemSetNotFoundException
   */
  function edit($id, array $data) {
    try {
      $npc = $this->get($id);
    } catch(ItemSetNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $npc->$key = $value;
    }
    $this->orm->itemSets->persistAndFlush($npc);
  }
  
  /**
   * Remove specified item set
   * 
   * @param int $id
   * @throws \Nexendrie\Model\ItemSetNotFoundException
   */
  function delete($id) {
    try {
      $set = $this->get($id);
    } catch(ItemSetNotFoundException $e) {
      throw $e;
    }
    $this->orm->itemSets->removeAndFlush($set);
  }
}
?>