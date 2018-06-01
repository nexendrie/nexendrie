<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nella\Forms\DateTime\DateInput;

/**
 *  Factory for form OpenDepositAccount
 *
 * @author Jakub Konečný
 */
final class OpenDepositAccountFormFactory {
  /** @var \Nexendrie\Model\Bank */
  protected $model;
  /** @var string */
  protected $dateTimeFormat;
  
  public function __construct(\Nexendrie\Model\Bank $model, \Nexendrie\Model\SettingsRepository $sr) {
    $this->model = $model;
    $this->dateTimeFormat = $sr->settings["locale"]["dateTimeFormat"];
  }
  
  public function create(): Form {
    $format = explode(" ", $this->dateTimeFormat);
    $maxDeposit = $this->model->maxDeposit();
    $form = new Form();
    $form->addText("amount", "Částka:")
      ->setRequired("Zadej částku.")
      ->addRule(Form::INTEGER, "Částka musí být celé číslo.")
      ->addRule(Form::RANGE, "Částka musí být v rozmezí 1-$maxDeposit.", [1, $maxDeposit]);
    $term = new DateInput($format[0], "Termín:");
    $term->setRequired("Zadej datum.");
    $term->addRule([$term, "validateDate"], "Neplatné datum.");
    $form->addComponent($term, "term");
    $form->onValidate[] = [$this, "validate"];
    $form->addSubmit("submit", "Otevřít účet");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function validate(Form $form, array $values): void {
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