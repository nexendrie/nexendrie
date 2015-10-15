<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditSkill
 *
 * @author Jakub Konečný
 */
class AddEditSkillFormFactory {
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addTextArea("description", "Popis")
      ->setRequired("Zadej popis.")
      ->addRule(Form::MAX_LENGTH, "Popis může mít maximálně 20 znaků.", 20);
    $form->addText("price", "Cena")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 1-999.", array(1, 999))
      ->setOption("description", "Cena na první úrovni");
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>