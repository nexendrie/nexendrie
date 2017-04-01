<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Meal as MealEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Tavern Model
 *
 * @author Jakub Konečný
 */
class Tavern {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all meals
   * 
   * @return MealEntity[]|ICollection
   */
  function listOfMeals(): ICollection {
    return $this->orm->meals->findAll();
  }
  
  /**
   * Get specified meal
   * 
   * @param int $id Meal's id
   * @return MealEntity
   * @throws MealNotFoundException
   */
  function getMeal(int $id): MealEntity {
    $meal = $this->orm->meals->getById($id);
    if($meal) {
      return $meal;
    } else {
      throw new MealNotFoundException;
    }
  }
  
  /**
   * Add new meal
   * 
   * @param array $data
   * @return void
   */
  function addMeal(array $data): void {
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
   * @throws MealNotFoundException
   */
  function editMeal(int $id, array $data): void {
    try {
      $meal = $this->getMeal($id);
    } catch(MealNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $meal->$key = $value;
    }
    $this->orm->meals->persistAndFlush($meal);
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
  function buyMeal(int $id): string {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $meal = $this->orm->meals->getById($id);
    if(!$meal) {
      throw new MealNotFoundException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $meal->price) {
      throw new InsufficientFundsException;
    }
    $message = $meal->message;
    $user->money -= $meal->price;
    $user->lastActive = time();
    if($meal->life != 0 AND $user->life > 1 AND $user->life < $user->maxLife) {
      $user->life += $meal->life;
      if($meal->life > 0) {
        $message .= " Přibylo ti $meal->life životů.";
      } else {
        $message .= " Ubylo ti " . $meal->life * -1 . " životů.";
      }
    }
    $this->orm->users->persistAndFlush($user);
    return $message;
  }
}
?>