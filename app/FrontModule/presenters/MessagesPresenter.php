<?php
namespace Nexendrie\FrontModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\NewMessageFormFactory,
    Nexendrie\Model\MessageNotFoundException,
    Nexendrie\Model\AccessDeniedException;

/**
 * Presenter Messages
 *
 * @author Jakub Konečný
 */
class MessagesPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Messenger @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresLogin();
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
    } catch(AccessDeniedException $e) {
      $this->forward("cannotshow");
    } catch(MessageNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param int|NULL $id Receiver's id
   * @return void
   */
  function actionNew($id = NULL) {
    
  }
  
  /**
   * Creates form for new message
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentNewMessageForm(NewMessageFormFactory $factory) {
    $form = $factory->create();
    try {
      $uid = $this->getParameter("id", NULL);
      $form["to"]->setDefaultValue($uid);
    } catch(\Nette\InvalidArgumentException $e) {
      
    }
    $form->onSuccess[] = function(Form $form, $values) {
      $this->flashMessage("Zpráva byla odeslána.");
      $this->redirect("Messages:sent");
    };
    return $form;
  }
}
?>