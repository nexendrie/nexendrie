<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditTown
 *
 * @author Jakub Konečný
 */
class AddEditTownFormFactory {
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  
  public function __construct(\Nexendrie\Model\Profile $profileModel) {
    $this->profileModel = $profileModel;
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSelect("owner", "Majitel:", $this->profileModel->getListOfLords())
      ->setRequired("Vyber majitele")
      ->setValue(0);
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>