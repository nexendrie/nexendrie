<?php
namespace Nexendrie\Presenters;

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
}
?>