<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\ItemSet as ItemSetEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * ItemSet Model
 *
 * @author Jakub Konečný
 */
class ItemSet {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  use \Nette\SmartObject;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get list of all item sets
   * 
   * @return ItemSetEntity[]|ICollection
   */
  function listOfSets(): ICollection {
    return $this->orm->itemSets->findAll();
  }
  
  /**
   * Get specified item set
   *  
   * @param int $id
   * @return ItemSetEntity
   * @throws ItemSetNotFoundException
   */
  function get(int $id): ItemSetEntity {
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
   * @throws ItemSetNotFoundException
   */
  function edit(int $id, array $data) {
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
   * @throws ItemSetNotFoundException
   */
  function delete(int $id) {
    try {
      $set = $this->get($id);
    } catch(ItemSetNotFoundException $e) {
      throw $e;
    }
    $this->orm->itemSets->removeAndFlush($set);
  }
}
?>