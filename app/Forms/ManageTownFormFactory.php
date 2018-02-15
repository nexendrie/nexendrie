<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form ManageTown
 *
 * @author Jakub Konečný
 */
class ManageTownFormFactory {
  /** @var \Nexendrie\Model\Town */
  protected $model;
  /** @var int */
  private $id;
  
  public function __construct(\Nexendrie\Model\Town $model) {
    $this->model = $model;
  }
  
  public function create(int $townId): Form {
    $form = new Form();
    $this->id = $townId;
    $town = $this->model->get($this->id);
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.")
      ->addRule(Form::MAX_LENGTH, "Popis může mít maximálně 40 znaků.", 40);
    $form->addText("price", "Cena:")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999999.", [0,999999]);
    $form->addCheckbox("onMarket", "Na prodej");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($town->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    $this->model->edit($this->id, $values);
  }
}
?>