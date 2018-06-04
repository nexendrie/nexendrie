<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\Mount;

/**
 * Factory for form AddEditMount
 *
 * @author Jakub Konečný
 */
final class AddEditMountFormFactory {
  /** @var \Nexendrie\Model\Mount */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Mount $model) {
    $this->model = $model;
  }
  
  /**
   * @return string[]
   */
  protected function getGenders(): array {
    return Mount::getGenders();
    
  }
  
  protected function getMountTypes(): array {
    return $this->model->listOfMountTypes()->fetchPairs("id", "name");
  }
  
  public function create(): Form {
    $form = new Form();
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
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999999.", [0,999999])
      ->setValue(0);
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>