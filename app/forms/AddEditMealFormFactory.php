<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditMeal
 *
 * @author Jakub Konečný
 */
class AddEditMealFormFactory {
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX, "Jméno může mít maximálně 15 znaků.", 15);
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
    return $form;
  }
}
?>