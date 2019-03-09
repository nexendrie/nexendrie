<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditAdventure
 *
 * @author Jakub Konečný
 */
final class AddEditAdventureFormFactory {
  /** @var \Nexendrie\Model\Adventure */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nexendrie\Orm\Adventure */
  protected $adventure;

  public function __construct(\Nexendrie\Model\Adventure $model, \Nexendrie\Orm\Model $orm) {
    $this->model = $model;
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
  
  public function create(?\Nexendrie\Orm\Adventure $adventure = null): Form {
    $this->adventure = $adventure;
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
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
    $form->onSuccess[] = [$this, "process"];
    if(!is_null($adventure)) {
      $form->setDefaults($adventure->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if(is_null($this->adventure)) {
      $this->model->addAdventure($values);
    } else {
      $this->model->editAdventure($this->adventure->id, $values);
    }
  }
}
?>