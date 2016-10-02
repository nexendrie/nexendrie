<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditAdventureEnemy
 *
 * @author Jakub Konečný
 */
class AddEditAdventureEnemyFormFactory {
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addText("order", "Pořadí:")
       ->setRequired("Zadej pořadí.")
       ->addRule(Form::INTEGER, "Pořadí musí být celé číslo.")
       ->addRule(Form::RANGE, "Pořadí musí být v rozmezí 1-9.", [1, 9]);
    $form->addText("hitpoints", "Životy:")
      ->setRequired("Zadej počet životů.")
      ->addRule(Form::INTEGER, "Počet životů musí být celé číslo.")
      ->addRule(Form::RANGE, "Počet životů musí být v rozmezí 1-999.", [1, 999]);
    $form->addText("strength", "Síla:")
      ->setRequired("Zadej sílu.")
      ->addRule(Form::INTEGER, "Síla musí být celé číslo.")
      ->addRule(Form::RANGE, "Síla musí být v rozmezí 1-99.", [1, 99]);
    $form->addText("armor", "Brnění:")
      ->setRequired("Zadej brnění.")
      ->addRule(Form::INTEGER, "Brnění musí být celé číslo.")
      ->addRule(Form::RANGE, "Brnění musí být v rozmezí 0-99.", [0, 99]);
    $form->addText("reward", "Odměna:")
      ->setRequired("Zadej odměnu.")
      ->addRule(Form::INTEGER, "Odměna musí být celé číslo.")
      ->addRule(Form::RANGE, "Odměna musí být v rozmezí 1-999.", [1, 999]);
    $form->addTextArea("encounterText", "Text při střetnutí:")
      ->setRequired("Zadej zext při střetnutí.");
    $form->addTextArea("victoryText", "Text při vítězství:")
      ->setRequired("Zadej text při vítězství.");
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>