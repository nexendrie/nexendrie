<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\ItemSet as ItemSetEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * ItemSet Model
 *
 * @author Jakub Konečný
 */
final class ItemSet {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get list of all item sets
   * 
   * @return ItemSetEntity[]|ICollection
   */
  public function listOfSets(): ICollection {
    return $this->orm->itemSets->findAll();
  }
  
  /**
   * Get specified item set
   *
   * @throws ItemSetNotFoundException
   */
  public function get(int $id): ItemSetEntity {
    $set = $this->orm->itemSets->getById($id);
    if($set === null) {
      throw new ItemSetNotFoundException();
    }
    return $set;
  }
  
  /**
   * Add new item set
   */
  public function add(array $data): void {
    $set = new ItemSetEntity();
    $this->orm->itemSets->attach($set);
    foreach($data as $key => $value) {
      $set->$key = $value;
    }
    $this->orm->itemSets->persistAndFlush($set);
  }
  
  /**
   * Edit specified item set
   *
   * @throws ItemSetNotFoundException
   */
  public function edit(int $id, array $data): void {
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
   * @throws ItemSetNotFoundException
   */
  public function delete(int $id): void {
    try {
      $set = $this->get($id);
    } catch(ItemSetNotFoundException $e) {
      throw $e;
    }
    $this->orm->itemSets->removeAndFlush($set);
  }
}
?>