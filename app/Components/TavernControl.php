<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\MealNotFoundException;
use Nexendrie\Model\InsufficientFundsException;

/**
 * TavernControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class TavernControl extends \Nette\Application\UI\Control {
  protected \Nexendrie\Model\Tavern $model;
  protected \Nette\Security\User $user;
  
  public function __construct(\Nexendrie\Model\Tavern $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->user = $user;
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/tavern.latte");
    if($this->user->isLoggedIn()) {
      $this->template->meals = $this->model->listOfMeals()
        ->orderBy("life")
        ->orderBy("price")
        ->orderBy("id");
    }
    $this->template->render();
  }
  
  public function handleEat(int $meal): void {
    try {
      $this->template->message = $this->model->buyMeal($meal);
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