<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditAdventure
 *
 * @author Jakub Konečný
 */
final class AddEditAdventureFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get list of events
   * 
   * @return array of id => name
   */
  protected function getEvents(): array {
    return $this->orm->events->findAll()->fetchPairs("id", "name");
  }
  
  public function create(): Form {
    $form = new Form();
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
      ->addRule(Form::RANGE, "Úroveň musí být v rozmezí 55-10000.", [55, 10000])
      ->setValue(55);
    $form->addText("reward", "Odměna:")
      ->setRequired("Zadej odměnu.")
      ->addRule(Form::INTEGER, "Odměna musí být celé číslo.")
      ->addRule(Form::RANGE, "Odměna musí být v rozmezí 1-999.", [1, 999]);
    $form->addSelect("event", "Akce:", $this->getEvents())
      ->setPrompt("žádná");
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>