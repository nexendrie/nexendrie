<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form TakeLoan
 *
 * @author Jakub Konečný
 */
class TakeLoanFormFactory {
  /** @var \Nexendrie\Model\Bank @autowire */
  protected $model;
  
  function __construct(\Nexendrie\Model\Bank $model) {
    $this->model = $model;
  }
  
  /**
   * @return Form
   */
  function create() {
    $maxLoan = $this->model->maxLoan();
    $form = new Form;
    $form->addText("amount", "Částka:")
      ->setRequired("Zadej částku.")
      ->addRule(Form::INTEGER, "Částka musí být celé číslo.")
      ->addRule(Form::RANGE, "Částka musí být v rozmezí 1-$maxLoan.", array(1, $maxLoan));
    $form->addSubmit("submit", "Půjčit si");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values) {
    $this->model->takeLoan($values["amount"]);
  }
}
?>