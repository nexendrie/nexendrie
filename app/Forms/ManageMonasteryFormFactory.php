<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\MonasteryNotFoundException,
    Nexendrie\Model\MonasteryNameInUseException,
    Nextras\Orm\Entity\IEntity;

/**
 * Factory for form ManageMonastery
 *
 * @author Jakub Konečný
 */
class ManageMonasteryFormFactory {
  /** @var \Nexendrie\Model\Monastery */
  protected $model;
  /** @var int */
  protected $id;
  
  function __construct(\Nexendrie\Model\Monastery $model) {
    $this->model = $model;
  }
  
  /**
   * @param int $id
   * @return Form
   */
  function create($id) {
    $this->id = $id;
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno");
    $form->addSelect("leader", "Vůdce:", $this->model->highClerics($id));
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($this->model->get($id)->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  function submitted(Form $form, array $values) {
    try {
      $this->model->edit($this->id, $values);
    } catch(MonasteryNotFoundException $e) {
      $form->addError("Neplatný klášter.");
    } catch(MonasteryNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>