<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nextras\Orm\Entity\IEntity;

/**
 * Factory for form AddEditMount
 *
 * @author Jakub Konečný
 */
class ManageMountFormFactory {
  /** @var \Nexendrie\Model\Mount */
  protected $model;
  /** @var int */
  private $id;
  
  function __construct(\Nexendrie\Model\Mount $model) {
    $this->model = $model;
  }
  
  /**
   * @param int $mountId
   * @return Form
   */
  function create($mountId) {
    $form = new Form;
    $this->id = $mountId;
    $mount = $this->model->get($this->id);
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addText("price", "Cena:")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999999.", [0,999999]);
    $form->addCheckbox("onMarket", "Na prodej");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($mount->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  function submitted(Form $form) {
    $this->model->edit($this->id, $form->getValues(true));
  }
}
?>