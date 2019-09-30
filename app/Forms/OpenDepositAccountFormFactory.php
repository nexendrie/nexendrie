<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nextras\Forms\Controls\DatePicker;

/**
 *  Factory for form OpenDepositAccount
 *
 * @author Jakub Konečný
 */
final class OpenDepositAccountFormFactory {
  /** @var \Nexendrie\Model\Bank */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Bank $model) {
    $this->model = $model;
  }
  
  public function create(): Form {
    $maxDeposit = $this->model->maxDeposit();
    $form = new Form();
    $form->addText("amount", "Částka:")
      ->setRequired("Zadej částku.")
      ->addRule(Form::INTEGER, "Částka musí být celé číslo.")
      ->addRule(Form::RANGE, "Částka musí být v rozmezí 1-$maxDeposit.", [1, $maxDeposit]);
    $term = new DatePicker("Termín:");
    $term->setRequired("Zadej datum.");
    $form->addComponent($term, "term");
    $form->addSubmit("submit", "Otevřít účet");
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function validate(Form $form, array $values): void {
    if($values["term"] === null) {
      return;
    }
    $term = $values["term"]->getTimestamp();
    if($term < time()) {
      $form->addError("Datum nemůže být v minulosti.");
    }
  }
  
  public function process(Form $form, array $values): void {
    $this->model->openDeposit($values["amount"], $values["term"]->getTimestamp());
  }
}
?>