<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Meal as MealEntity;

/**
 * Tavern Model
 *
 * @author Jakub Konečný
 */
class Tavern extends \Nette\Object {
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
  
  /**
   * Buy a meal
   * 
   * @param int $id Meal's id
   * @return string
   * @throws AuthenticationNeededException
   * @throws MealNotFoundException
   * @throws InsufficientFundsException
   */
  function buyMeal($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $meal = $this->orm->meals->getById($id);
    if(!$meal) throw new MealNotFoundException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $meal->price) throw new InsufficientFundsException;
    $message = $meal->message;
    $user->money -= $meal->price;
    $user->lastActive = time();
    if($meal->life != 0 AND $user->life > 1 AND $user->life < $user->maxLife) {
      $user->life += $meal->life;
      if($meal->life > 0) $message .= " Přibylo ti $meal->life životů.";
      else $message .= " Ubylo ti " . $meal->life * -1 . " životů.";
    }
    $this->orm->users->persistAndFlush($user);
    return $message;
  }
}

class MealNotFoundException extends RecordNotFoundException {
  
}
?>