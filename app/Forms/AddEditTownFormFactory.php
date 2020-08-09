<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditTown
 *
 * @author Jakub Konečný
 */
final class AddEditTownFormFactory {
  protected \Nexendrie\Model\Town $model;
  protected \Nexendrie\Model\Profile $profileModel;
  protected ?\Nexendrie\Orm\Town $town;
  
  public function __construct(\Nexendrie\Model\Town $model, \Nexendrie\Model\Profile $profileModel) {
    $this->model = $model;
    $this->profileModel = $profileModel;
  }
  
  public function create(?\Nexendrie\Orm\Town $town = null): Form {
    $this->town = $town;
    $form = new Form();
    $form->addText("name", "Jméno")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSelect("owner", "Majitel:", $this->profileModel->getListOfLords())
      ->setRequired("Vyber majitele.")
      ->setValue(0);
    if($town === null) {
      $form->addCheckbox("onMarket", "Na prodej");
      $form->addText("price", "Cena:")
        ->setRequired("Zadej cenu.")
        ->addRule(Form::INTEGER, "Cena musí být celé číslo")
        ->addRule(Form::MIN, "Cena musí být větší než 0.", 1)
        ->setDefaultValue(5000);
    }
    $form->addSubmit("submit", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    if($town !== null) {
      $form->setDefaults($town->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if($this->town === null) {
      $this->model->add($values);
    } else {
      $this->model->edit($this->town->id, $values);
    }
  }
}
?>