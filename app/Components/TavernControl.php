<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\AuthenticationNeededException,
    Nexendrie\Model\MealNotFoundException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * TavernControl
 *
 * @author Jakub Konečný
 */
class TavernControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Tavern */
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Tavern $model, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function render(): void {
    $this->template->setFile(__DIR__ . "/tavern.latte");
    if($this->user->isLoggedIn()) {
      $this->template->meals = $this->model->listOfMeals()
        ->orderBy("life")
        ->orderBy("price");
    }
    $this->template->render();
  }
  
  /**
   * @param int $mealId
   * @return void
   */
  function handleEat(int $mealId): void {
    try {
      $this->template->message = $this->model->buyMeal($mealId);
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("Musíš být přihlášený");
    } catch(MealNotFoundException $e) {
      $this->flashMessage("Jídlo nenalezeno.");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nemáš dostatek peněz.");
    }
    $this->presenter->redirect("default");
  }
}
?>