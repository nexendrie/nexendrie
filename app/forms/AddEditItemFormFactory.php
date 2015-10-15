<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditItem
 *
 * @author Jakub Konečný
 */
class AddEditItemFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @return array
   */
  protected function getShops() {
    $shops = $this->orm->shops->findAll();
    $return = array();
    foreach($shops as $shop) {
      $return[$shop->id] = $shop->name;
    }
    return $return;
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 30 znaků.", 30);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addText("price", "Cena")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999.", array(0, 999));
    $form->addSelect("shop", "Obchod", $this->getShops())
      ->setRequired("Vyber obchod.");
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>