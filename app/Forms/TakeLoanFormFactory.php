<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form TakeLoan
 *
 * @author Jakub Konečný
 */
final class TakeLoanFormFactory {
  /** @var \Nexendrie\Model\Bank */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Bank $model) {
    $this->model = $model;
  }
  
  public function create(): Form {
    $maxLoan = $this->model->maxLoan();
    $form = new Form();
    $form->addText("amount", "Částka:")
      ->setRequired("Zadej částku.")
      ->addRule(Form::INTEGER, "Částka musí být celé číslo.")
      ->addRule(Form::RANGE, "Částka musí být v rozmezí 1-$maxLoan.", [1, $maxLoan]);
    $form->addSubmit("submit", "Půjčit si");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    $this->model->takeLoan($values["amount"]);
  }
}
?>