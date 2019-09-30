<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditMeal
 *
 * @author Jakub Konečný
 */
final class AddEditMealFormFactory {
  /** @var \Nexendrie\Model\Tavern */
  protected $model;
  /** @var \Nexendrie\Orm\Meal */
  protected $meal;

  public function __construct(\Nexendrie\Model\Tavern $model) {
    $this->model = $model;
  }

  public function create(?\Nexendrie\Orm\Meal $meal = null): Form {
    $this->meal = $meal;
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 15 znaků.", 15);
    $form->addTextArea("message", "Zpráva:")
      ->setRequired("Zadej zprávu.");
    $form->addText("price", "Cena:")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 1-999.", [1, 999]);
    $form->addText("life", "Efekt na život:")
      ->setRequired("Zadej efekt.")
      ->addRule(Form::INTEGER, "Efekt musí být celé číslo.")
      ->addRule(Form::RANGE, "Efekt musí být v rozmezí -60 - 60.", [-60, 60]);
    $form->addSubmit("submit", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    if($meal !== null) {
      $form->setDefaults($meal->toArray());
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if($this->meal === null) {
      $this->model->addMeal($values);
    } else {
      $this->model->editMeal($this->meal->id, $values);
    }
  }
}
?>