<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Meal as MealEntity;

/**
 * Pub Model
 *
 * @author Jakub Konečný
 */
class Pub extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all meals
   * 
   * @return MealEntity[]
   */
  function listOfMeals() {
    return $this->orm->meals->findAll();
  }
  
  /**
   * Get specified meal
   * 
   * @param int $id Meal's id
   * @return MealEntity
   * @throws MealNotFoundException
   */
  function getMeal($id) {
    $meal = $this->orm->meals->getById($id);
    if($meal) return $meal;
    else throw new MealNotFoundException;
  }
  
  /**
   * Add new meal
   * 
   * @param array $data
   * @return void
   */
  function addMeal(array $data) {
    $meal = new MealEntity;
    foreach($data as $key => $value) {
      $meal->$key = $value;
    }
    $this->orm->meals->persistAndFlush($meal);
  }
  
  /**
   * Edit specified meal
   * 
   * @param int $id Meal's id
   * @param array $data
   * @return void
   */
  function editMeal($id, array $data) {
    $job = $this->orm->meals->getById($id);
    foreach($data as $key => $value) {
      $job->$key = $value;
    }
    $this->orm->meals->persistAndFlush($job);
  }
}

class MealNotFoundException extends RecordNotFoundException {
  
}
?>