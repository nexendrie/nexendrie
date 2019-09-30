<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Meal as MealEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Tavern Model
 *
 * @author Jakub Konečný
 */
final class Tavern {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all meals
   * 
   * @return MealEntity[]|ICollection
   */
  public function listOfMeals(): ICollection {
    return $this->orm->meals->findAll();
  }
  
  /**
   * Get specified meal
   *
   * @throws MealNotFoundException
   */
  public function getMeal(int $id): MealEntity {
    $meal = $this->orm->meals->getById($id);
    if($meal === null) {
      throw new MealNotFoundException();
    }
    return $meal;
  }
  
  /**
   * Add new meal
   */
  public function addMeal(array $data): void {
    $meal = new MealEntity();
    foreach($data as $key => $value) {
      $meal->$key = $value;
    }
    $this->orm->meals->persistAndFlush($meal);
  }
  
  /**
   * Edit specified meal
   *
   * @throws MealNotFoundException
   */
  public function editMeal(int $id, array $data): void {
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
   * @throws AuthenticationNeededException
   * @throws MealNotFoundException
   * @throws InsufficientFundsException
   */
  public function buyMeal(int $id): string {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $meal = $this->orm->meals->getById($id);
    if($meal === null) {
      throw new MealNotFoundException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $meal->price) {
      throw new InsufficientFundsException();
    }
    $message = $meal->message;
    $user->money -= $meal->price;
    $user->lastActive = time();
    if($meal->life !== 0 && $user->life > 1 && $user->life < $user->maxLife) {
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