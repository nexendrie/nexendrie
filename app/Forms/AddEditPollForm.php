<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditPoll
 *
 * @author Jakub Konečný
 */
class AddEditPollFormFactory {
  /**
   * @return \Nette\Application\UI\Form
   */
  function create() {
    $form = new Form;
    $form->addText("question", "Otázka:")
      ->addRule(Form::MAX_LENGTH, "Otázka může mít maximálně 60 znaků.", 60)
      ->setRequired("Zadej otázku.");
    $form->addTextArea("answers", "Odpovědi:")
      ->setRequired("Zadej alespoň jednu odpověď.")
      ->setOption("description", "Každou odpověď napiš na nový řádek.");
    $form->addCheckbox("locked", "Uzamčená");
    $form->addSubmit("send", "Odeslat");
    return $form;
  }
}
?>