<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditJobMessage
 *
 * @author Jakub Konečný
 */
class AddEditJobMessageFormFactory {
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addTextArea("message", "Zpráva:")
      ->setRequired("Zadej zprávu.");
    $form->addCheckbox("success", "Zobrazit při úspěchu");
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>