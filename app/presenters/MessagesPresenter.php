<?php
namespace Nexendrie\Presenters;

use Nette\Application\UI;

/**
 * Presenter Messages
 *
 * @author Jakub Konečný
 */
class MessagesPresenter extends BasePresenter {
  /** @var \Nexendrie\Messenger */
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->model = $this->context->getService("model.messenger");
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->messages = $this->model->inbox();
  }
  
  /**
   * @return void
   */
  function renderSent() {
    $this->template->messages = $this->model->outbox();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderView($id) {
    try {
      $this->template->message = $this->model->show($id);
    } catch (\Nette\Application\ForbiddenRequestException $e) {
      if($e->getCode() === 403) {
        $this->forward("cannotshow");
      }
    } catch(\Nette\Application\BadRequestException $e) {
      $this->forward("notfound");
    }
  }
  
  function actionNew($id = NULL) {
    
  }
  
  /**
   * Creates form for new message
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentNewMessageForm() {
    $form = new UI\Form;
    $users = $this->model->usersList();
    try {
    $uid = $this->getParameter("id", NULL);
    $form->addSelect("to", "Pro:", $users)
      ->setPrompt("Vyber příjemce")
      ->setRequired("Vyber příjemce.")
      ->setDefaultValue($uid);
    } catch(\Nette\InvalidArgumentException $e) {
      
    }
    $form->addText("subject", "Předmět:")
      ->addRule(UI\Form::MAX_LENGTH, "Předmět může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej předmět.");
    $form->addTextArea("text", "Text:")
      ->setRequired("Zadej text.");
    $form->addSubmit("send", "Odeslat");
    $form->onSuccess[] = array($this, "newMessageFormSucceeded");
    return $form;
  }
  
  /**
   * Send new message
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   * @return void
   */
  function newMessageFormSucceeded(UI\Form $form, $values) {
    $this->model->send($values);
    $this->flashMessage("Zpráva byla odeslána.");
    $this->redirect("Messages:sent");
  }
}
?>