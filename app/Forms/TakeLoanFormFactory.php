<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Bank;
use Nexendrie\Model\TooHighLoanException;

/**
 * Factory for form TakeLoan
 *
 * @author Jakub Konečný
 */
final class TakeLoanFormFactory {
  public function __construct(private readonly Bank $model) {
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("amount", "Částka:")
      ->setRequired("Zadej částku.")
      ->addRule(Form::INTEGER, "Částka musí být celé číslo.")
      ->addRule(Form::MIN, "Částka musí být větší než 0.", 1);
    $form->addSubmit("submit", "Půjčit si");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->takeLoan($values["amount"]);
    } catch(TooHighLoanException $e) {
      $form->addError("Částka musí být menší než " . $e->getCode() . ".");
    }
  }
}
?>