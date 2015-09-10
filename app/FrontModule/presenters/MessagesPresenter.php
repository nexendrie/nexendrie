<?php
namespace Nexendrie\FrontModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\NewMessageFormFactory;

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