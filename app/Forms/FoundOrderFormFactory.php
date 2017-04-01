<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\CannotFoundOrderException,
    Nexendrie\Model\OrderNameInUseException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * Factory for form FoundOrder
 *
 * @author Jakub Konečný
 */
class FoundOrderFormFactory {
  /** @var \Nexendrie\Model\Order */
  protected $model;
  
  function __construct(\Nexendrie\Model\Order $model) {
    $this->model = $model;
  }
  
  /**
   * @return Form
   */
  function create(): Form {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Založit");
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