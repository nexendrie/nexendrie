<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\NewMessageFormFactory;
use Nexendrie\Model\MessageNotFoundException;
use Nexendrie\Model\AccessDeniedException;
use Nexendrie\Model\Messenger;

/**
 * Presenter Messages
 *
 * @author Jakub Konečný
 */
final class MessagesPresenter extends BasePresenter {
  protected bool $publicCache = false;
  
  public function __construct(private readonly Messenger $model) {
    parent::__construct();
  }
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
  }
  
  public function renderDefault(): void {
    $this->template->messages = $this->model->inbox();
  }
  
  public function renderSent(): void {
    $this->template->messages = $this->model->outbox();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderView(int $id): void {
    try {
      $this->template->message = $this->model->show($id);
    } catch(AccessDeniedException) {
      $this->forward("cannotshow");
    } catch(MessageNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  /**
   * @param int|null $id Receiver's id
   */
  public function actionNew(int $id = null): void {
  }
  
  protected function createComponentNewMessageForm(NewMessageFormFactory $factory): Form {
    $form = $factory->create();
    try {
      $uid = (int) $this->getParameter("id", null);
      /** @var \Nette\Forms\Controls\SelectBox $receiver */
      $receiver = $form["to"];
      $receiver->setDefaultValue($uid);
    } catch(\Nette\InvalidArgumentException $e) {
      
    }
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Zpráva byla odeslána.");
      $this->redirect("Messages:sent");
    };
    return $form;
  }

  protected function getDataModifiedTime(): int {
    if(isset($this->template->message)) {
      return $this->template->message->created;
    }
    return 0;
  }
}
?>