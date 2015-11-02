<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditAdventure
 *
 * @author Jakub Konečný
 */
class AddEditAdventureFormFactory {
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addTextArea("intro", "Úvodní text:")
      ->setRequired("Zadej úvodní text.");
    $form->addTextArea("epilogue", "Závěrečný text:")
      ->setRequired("Zadej zavérečný text.");
    $form->addText("level", "Úroveň:")
      ->setRequired("Zadej úroveň.")
      ->addRule(Form::INTEGER, "Úroveň musí být celé číslo.")
      ->addRule(Form::RANGE, "Úroveň musí být v rozmezí 55-10000.", array(55, 10000))
      ->setValue(55);
    $form->addText("reward", "Odměna:")
      ->setRequired("Zadej odměnu.")
      ->addRule(Form::INTEGER, "Odměna musí být celé číslo.")
      ->addRule(Form::RANGE, "Odměna musí být v rozmezí 1-999.", array(1, 999));
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>