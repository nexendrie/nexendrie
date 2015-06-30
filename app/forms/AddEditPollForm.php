<?php
namespace Nexendrie\Forms;

use Nette\Application\UI;

/**
 * Factory for form AddEditPoll
 *
 * @author Jakub Konečný
 */
class AddEditPollForm {
  /**
   * @return \Nette\Application\UI\Form
   */
  function create() {
    $form = new UI\Form;
    $form->addText("question", "Otázka:")
      ->addRule(UI\Form::MAX_LENGTH, "Otázka může mít maximálně 60 znaků.", 60)
      ->setRequired("Zadej otázku.");
    $form->addTextArea("anwsers", "Odpovědi:")
      ->setRequired("Zadej alespoň jednu odpoveď.")
      ->setOption("description", "Každou odpověď napiš na nový řádek.");
    $form->addCheckbox("locked", "Uzamčená");
    $form->addSubmit("send", "Odeslat");
    return $form;
  }
}
?>