<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditJob
 *
 * @author Jakub Konečný
 */
class AddEditJobFormFactory {
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.")
      ->setOption("description", "Zobrazí se v seznamu prací.");
    $form->addTextArea("help", "Nápověda:")
      ->setRequired("Zadej nápověda.")
      ->setOption("description", "Zobrazí se během práce.");
    $form->addText("count", "Počet:")
      ->setRequired("Zadej počet.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->setValue(0);
    $form->addText("award", "Odměna:")
      ->setRequired("Zadej odměnu.")
      ->addRule(Form::INTEGER, "Odměna musí být celé číslo.")
      ->setOption("description", "Odměna za dokončení práce/1 jednotku.");
    $form->addText("level", "Úroveň:")
      ->setRequired("Zadej úroveň.")
      ->addRule(Form::INTEGER, "Úroveň musí být celé číslo.")
      ->addRule(Form::RANGE, "Úroveň musí být v rozmezí 50-10000.", array(50, 10000))
      ->setValue(50)
      ->setOption("description", "Minimální úroveň pro výkon práce.");
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>