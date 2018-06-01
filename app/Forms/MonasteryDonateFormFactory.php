<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\NotInMonasteryException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * Factory for form MonasteryDonate
 *
 * @author Jakub Konečný
 */
final class MonasteryDonateFormFactory {
  /** @var \Nexendrie\Model\Monastery */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Monastery $model) {
    $this->model = $model;
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("amount", "Množství:")
      ->setRequired("Zadej množství.")
      ->addRule(Form::INTEGER, "Množství musí být celé číslo")
      ->addRule(Form::MIN, "Musíš darovat minimálně 1 groš.", 1);
    $form->addSubmit("submit", "Darovat");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->donate($values["amount"]);
    } catch(NotInMonasteryException $e) {
      $form->addError("Nejsi v klášteře.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    }
  }
}
?>