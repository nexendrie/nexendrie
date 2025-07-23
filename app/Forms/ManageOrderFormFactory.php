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
  private int $id;
  
  public function __construct(private readonly \Nexendrie\Model\Order $model) {
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
    } catch(OrderNameInUseException) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>