<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\CannotFoundOrderException;
use Nexendrie\Model\Order;
use Nexendrie\Model\OrderNameInUseException;
use Nexendrie\Model\InsufficientFundsException;

/**
 * Factory for form FoundOrder
 *
 * @author Jakub Konečný
 */
final class FoundOrderFormFactory {
  public function __construct(private readonly Order $model) {
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Založit");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->found($values);
    } catch(CannotFoundOrderException $e) {
      $form->addError("Nemůžeš založit řád.");
    } catch(OrderNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    }
  }
}
?>