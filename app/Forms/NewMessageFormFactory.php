<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form NewMessageForm
 *
 * @author Jakub Konečný
 */
final class NewMessageFormFactory {
  protected \Nexendrie\Model\Messenger $model;
  
  public function __construct(\Nexendrie\Model\Messenger $model) {
    $this->model = $model;
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addSelect("to", "Pro:", $this->model->usersList())
      ->setPrompt("Vyber příjemce")
      ->setRequired("Vyber příjemce.");
    $form->addText("subject", "Předmět:")
      ->addRule(Form::MAX_LENGTH, "Předmět může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej předmět.");
    $form->addTextArea("text", "Text:")
      ->setRequired("Zadej text.");
    $form->addSubmit("send", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    $this->model->send($values);
  }
}
?>