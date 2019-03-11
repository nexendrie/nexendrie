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
  /** @var \Nexendrie\Model\Town */
  protected $model;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  /** @var \Nexendrie\Orm\Town|null */
  protected $town;
  
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
    if(is_null($town)) {
      $form->addCheckbox("onMarket", "Na prodej");
      $form->addText("price", "Cena:")
        ->setRequired("Zadej cenu.")
        ->addRule(Form::INTEGER, "Cena musí být celé číslo")
        ->addRule(Form::MIN, "Cena musí být větší než 0.", 1)
        ->setDefaultValue(5000);
    }
    $form->addSubmit("submit", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    if(!is_null($town)) {
      $form->setDefaults($town->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if(is_null($this->town)) {
      $this->model->add($values);
    } else {
      $this->model->edit($this->town->id, $values);
    }
  }
}
?>