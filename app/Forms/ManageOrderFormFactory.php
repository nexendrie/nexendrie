<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\OrderNameInUseException;

/**
 * Factory for form ManageOrder
 *
 * @author Jakub Konečný
 */
class ManageOrderFormFactory {
  /** @var \Nexendrie\Model\Order */
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  private $id;
  
  function __construct(\Nexendrie\Model\Order $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->user = $user;
  }
  
  /**
   * @param int $guildId
   * @return Form
   */
  function create(int $guildId): Form {
    $form = new Form;
    $this->id = $guildId;
    $guild = $this->model->getOrder($this->id);
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($guild->toArray());
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function process(Form $form, array $values): void {
    try {
      $this->model->editOrder($this->id, $values);
    } catch(OrderNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>