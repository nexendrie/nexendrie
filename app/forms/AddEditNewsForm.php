<?php
namespace Nexendrie\Forms;

use Nette\Application\UI;

/**
 * Factory for form AddEditNews
 * 
 * @author Jakub Konečný
 */
class AddEditNewsForm {
  /**
   * @return \Nette\Application\UI\Form
   */
  static function create() {
    $form = new UI\Form;
    $form->addText("title", "Titulek:")
      ->addRule(UI\Form::MAX_LENGTH, "Titulek může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titulek.");
    $form->addTextArea("text", "Text:")
      ->setRequired("Zadej text.");
    $form->addCheckbox("comments", "Povolit komentáře")
      ->setValue(true);
    $form->addSubmit("send", "Odeslat");
    return $form;
  }
}
?>