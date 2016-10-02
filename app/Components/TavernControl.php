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
    $this->model = $model;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/tavern.latte");
    if($this->user->isLoggedIn()) {
      $template->meals = $this->model->listOfMeals()
        ->orderBy("life")
        ->orderBy("price");
    }
    $template->render();
  }
  
  /**
   * @param int $mealId
   * @return void
   */
  function handleEat($mealId) {
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

interface TavernControlFactory {
  /** @return TavernControl */
  function create();
}
?>