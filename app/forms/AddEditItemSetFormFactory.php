<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\ItemSet;

/**
 * Factory for form AddEditItemSet
 *
 * @author Jakub Konečný
 */
class AddEditItemSetFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  protected function getWeapons() {
    return $this->orm->items->findWeapons()->fetchPairs("id", "name");
  }
  
  protected function getArmors() {
    return $this->orm->items->findArmors()->fetchPairs("id", "name");
  }
  
  protected function getHelmets() {
    return $this->orm->items->findHelmets()->fetchPairs("id", "name");
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 30 znaků.", 30);
    $form->addSelect("weapon", "Zbraň:", $this->getWeapons())
      ->setPrompt("");
    $form->addSelect("armor", "Zbroj:", $this->getArmors())
      ->setPrompt("");
    $form->addSelect("helmet", "Přilba:", $this->getHelmets())
      ->setPrompt("");;
    $form->addSelect("stat", "Vlastnost:", ItemSet::getStats())
      ->setRequired("Vyber vlastnost.");
    $form->addText("bonus", "Velikost bonusu:")
      ->setRequired("Zadej velikost bonusu.")
      ->addRule(Form::INTEGER, "Velikost bonusu musí být celé číslo.")
      ->addRule(Form::RANGE, "Velikost bonusu musí být v rozmezí 0-99.", array(0, 99))
      ->setValue(0);
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>