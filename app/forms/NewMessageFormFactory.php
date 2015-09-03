<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nette\Utils\ArrayHash;

/**
 * Factory for form NewMessageForm
 *
 * @author Jakub Konečný
 */
class NewMessageFormFactory {
  /** @var \Nexendrie\Model\Messenger */
  protected $model;
  
  function __construct(\Nexendrie\Model\Messenger $model) {
    $this->model = $model;
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addSelect("to", "Pro:", $this->model->usersList())
      ->setPrompt("Vyber příjemce")
      ->setRequired("Vyber příjemce.");
    $form->addText("subject", "Předmět:")
      ->addRule(Form::MAX_LENGTH, "Předmět může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej předmět.");
    $form->addTextArea("text", "Text:")
      ->setRequired("Zadej text.");
    $form->addSubmit("send", "Odeslat");
    $form->onSuccess[] = function (Form $form, ArrayHash $values) {
      $this->model->send($values);
    };
    return $form;
  }
}
?>