<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

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
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault(): void {
    $this->template->messages = $this->model->inbox();
  }
  
  /**
   * @return void
   */
  function renderSent(): void {
    $this->template->messages = $this->model->outbox();
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function renderView(int $id): void {
    try {
      $this->template->message = $this->model->show($id);
    } catch(AccessDeniedException $e) {
      $this->forward("cannotshow");
    } catch(MessageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param int|NULL $id Receiver's id
   * @return void
   */
  function actionNew(int $id = NULL): void {
    
  }
  
  /**
   * @param NewMessageFormFactory $factory
   * @return Form
   */
  protected function createComponentNewMessageForm(NewMessageFormFactory $factory): Form {
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