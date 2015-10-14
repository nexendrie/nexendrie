<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\Mount;

/**
 * Factory for form AddEditMount
 *
 * @author Jakub Konečný
 */
class AddEditMountFormFactory {
  /** @var \Nexendrie\Model\Mount */
  protected $model;
  
  function __construct(\Nexendrie\Model\Mount $model) {
    $this->model = $model;
  }
  
  /**
   * @return string[]
   */
  protected function getGenders() {
    return Mount::getGenders();
    
  }
  
  /**
   * @return array
   */
  protected function getMountTypes() {
    $return = array();
    $types = $this->model->listOfMountTypes();
    foreach($types as $type) {
      $return[$type->id] = $type->name;
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
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addRadioList("gender", "Pohlaví:", $this->getGenders())
      ->setRequired("Vyber pohlaví.")
      ->setValue(Mount::GENDER_YOUNG);
    $form->addSelect("type", "Druh:", $this->getMountTypes())
      ->setRequired("Vyber druh.");
    $form->addText("price", "Cena:")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999999.", array(0,999999));
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>