<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\OrderNameInUseException;

/**
 * Factory for form ManageOrder
 *
 * @author Jakub Konečný
 */
final class ManageOrderFormFactory {
  protected \Nexendrie\Model\Order $model;
  protected \Nette\Security\User $user;
  private int $id;
  
  public function __construct(\Nexendrie\Model\Order $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->user = $user;
  }
  
  public function create(int $orderId): Form {
    $form = new Form();
    $this->id = $orderId;
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
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->editOrder($this->id, $values);
    } catch(OrderNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>