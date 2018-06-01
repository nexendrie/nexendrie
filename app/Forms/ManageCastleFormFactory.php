<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\CastleNameInUseException;

/**
 * Factory for form ManageCastle
 *
 * @author Jakub Konečný
 */
final class ManageCastleFormFactory {
  /** @var \Nexendrie\Model\Castle */
  protected $model;
  /** @var int */
  private $id;
  
  public function __construct(\Nexendrie\Model\Castle $model) {
    $this->model = $model;
  }
  
  public function create(int $castleId): Form {
    $form = new Form();
    $this->id = $castleId;
    $castle = $this->model->getCastle($this->id);
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($castle->toArray());
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->editCastle($this->id, $values);
    } catch(CastleNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>