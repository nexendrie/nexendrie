<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Chat\IOrderChatControlFactory;
use Nexendrie\Chat\OrderChatControl;
use Nexendrie\Forms\FoundOrderFormFactory;
use Nexendrie\Forms\ManageOrderFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\Locale;
use Nexendrie\Model\Order;
use Nexendrie\Model\OrderNotFoundException;
use Nexendrie\Model\CannotJoinOrderException;
use Nexendrie\Model\CannotLeaveOrderException;
use Nexendrie\Model\CannotUpgradeOrderException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\MissingPermissionsException;
use Nexendrie\Model\UserNotFoundException;
use Nexendrie\Model\UserNotInYourOrderException;
use Nexendrie\Model\CannotPromoteMemberException;
use Nexendrie\Model\CannotDemoteMemberException;
use Nexendrie\Model\CannotKickMemberException;

/**
 * Presenter Order
 *
 * @author Jakub Konečný
 */
final class OrderPresenter extends BasePresenter {
  public function __construct(private readonly Order $model, private readonly Locale $localeModel, private readonly IOrderChatControlFactory $chatFactory) {
    parent::__construct();
  }
  
  protected function startup(): void {
    parent::startup();
    if($this->action !== "detail" && $this->action !== "list") {
      $this->requiresLogin();
    }
  }

  protected function getChat(): OrderChatControl {
    return $this->chatFactory->create();
  }
  
  public function renderDefault(): void {
    $order = $this->model->getUserOrder();
    if($order === null) {
      $this->flashMessage("Nejsi v řádu.");
      $this->redirect("Homepage:");
    }
    $this->publicCache = false;
    $this->template->order = $order;
    $this->template->canLeave = $this->model->canLeave();
    $this->template->canManage = $this->model->canManage();
  }
  
  public function renderList(): void {
    $this->template->orders = $this->model->listOfOrders();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderDetail(int $id): void {
    try {
      $this->template->order = $this->model->getOrder($id);
    } catch(OrderNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionFound(): void {
    if(!$this->model->canFound()) {
      $this->flashMessage("Nemůžeš založit řád.");
      $this->redirect("Homepage:");
    }
    $this->template->foundingPrice = $this->sr->settings["fees"]["foundOrder"];
  }
  
  protected function createComponentFoundOrderForm(FoundOrderFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Řád založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  public function actionChat(): void {
    $order = $this->model->getUserOrder();
    if($order === null) {
      $this->flashMessage("Nejsi v řádu.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionJoin(int $id): never {
    try {
      $this->model->join($id);
      $message = $this->localeModel->genderMessage("Vstoupil(a) jsi do řádu.");
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinOrderException) {
      $this->flashMessage("Nemůžeš vstoupit do řádu.");
      $this->redirect("Homepage:");
    } catch(OrderNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionLeave(): never {
    try {
      $this->model->leave();
      $message = $this->localeModel->genderMessage("Opustil(a) jsi řádu.");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveOrderException) {
      $this->flashMessage("Nemůžeš opustit cech.");
      $this->redirect("Homepage:");
    }
  }
  
  public function actionManage(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat řád.");
      $this->redirect("Homepage:");
    }
    $this->template->order = $this->model->getUserOrder();
    $this->template->canUpgrade = $this->model->canUpgrade();
  }
  
  protected function createComponentManageOrderForm(ManageOrderFormFactory $factory): Form {
    /** @var \Nexendrie\Orm\Order $order */
    $order = $this->model->getUserOrder();
    $form = $factory->create($order->id);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  public function handleUpgrade(): never {
    try {
      $this->model->upgrade();
      $this->flashMessage("Řád vylepšen.");
      $this->redirect("manage");
    } catch(CannotUpgradeOrderException) {
      $this->flashMessage("Nemůžeš vylepšit řád.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
  
  public function actionMembers(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat řád.");
      $this->redirect("Homepage:");
    }
    /** @var \Nexendrie\Orm\Order $order */
    $order = $this->model->getUserOrder();
    $this->template->members = $this->model->getMembers($order->id);
    $this->template->maxRank = $this->model->maxRank;
  }
  
  public function handlePromote(int $user): never {
    try {
      $this->model->promote($user);
      $this->flashMessage("Povýšen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourOrderException) {
      $this->flashMessage("Uživatel není ve tvém řádu.");
      $this->redirect("Homepage:");
    } catch(CannotPromoteMemberException) {
      $this->flashMessage("Uživatel nemůže být povýšen.");
      $this->redirect("members");
    }
  }
  
  public function handleDemote(int $user): never {
    try {
      $this->model->demote($user);
      $this->flashMessage("Degradován(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourOrderException) {
      $this->flashMessage("Uživatel není ve tvém řádu.");
      $this->redirect("Homepage:");
    } catch(CannotDemoteMemberException) {
      $this->flashMessage("Uživatel nemůže být degradován.");
      $this->redirect("members");
    }
  }
  
  public function handleKick(int $user): never {
    try {
      $this->model->kick($user);
      $this->flashMessage("Vyloučen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourOrderException) {
      $this->flashMessage("Uživatel není ve tvém řádu.");
      $this->redirect("Homepage:");
    } catch(CannotKickMemberException) {
      $this->flashMessage("Uživatel nemůže být vyloučen.");
      $this->redirect("members");
    }
  }

  protected function getDataModifiedTime(): int {
    if(isset($this->template->order)) {
      return($this->template->order->updated);
    }
    if(isset($this->template->orders)) {
      $time = 0;
      /** @var \Nexendrie\Orm\Order $order */
      foreach($this->template->orders as $order) {
        $time = max($time, $order->updated);
      }
      return $time;
    }
    return 0;
  }
}
?>