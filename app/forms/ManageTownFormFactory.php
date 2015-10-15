<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

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
  
  function __construct(\Nexendrie\Model\Town $model) {
    $this->model = $model;
  }
  
  /**
   * @param int $townId
   * @return Form
   */
  function create($townId) {
    $form = new Form;
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
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999999.", array(0,999999));
    $form->addCheckbox("onMarket", "Na prodej");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($town->dummyArray());
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @return void
   */
  function submitted(Form $form) {
    $this->model->edit($this->id, $form->getValues(true));
  }
}
?>