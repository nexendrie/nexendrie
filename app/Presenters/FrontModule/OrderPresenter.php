<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\FoundOrderFormFactory,
    Nexendrie\Forms\ManageOrderFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\OrderNotFoundException,
    Nexendrie\Model\CannotJoinOrderException,
    Nexendrie\Model\CannotLeaveOrderException,
    Nexendrie\Model\CannotUpgradeOrderException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\AuthenticationNeededException,
    Nexendrie\Model\MissingPermissionsException,
    Nexendrie\Model\UserNotFoundException,
    Nexendrie\Model\UserNotInYourOrderException,
    Nexendrie\Model\CannotPromoteMemberException,
    Nexendrie\Model\CannotDemoteMemberException,
    Nexendrie\Model\CannotKickMemberException;

/**
 * Presenter Order
 *
 * @author Jakub Konečný
 */
class OrderPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Order @autowire  */
  protected $model;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  
  protected function startup(): void {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") {
      $this->requiresLogin();
    }
  }
  
  public function renderDefault(): void {
    $order = $this->model->getUserOrder();
    if(!$order) {
      $this->flashMessage("Nejsi v řádu.");
      $this->redirect("Homepage:");
    }
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
    } catch(OrderNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  public function actionFound(): void {
    if(!$this->model->canFound()) {
      $this->flashMessage("Nemůžeš založit řád.");
      $this->redirect("Homepage:");
    }
    $this->template->foundingPrice = $this->localeModel->money($this->model->foundingPrice);
  }
  
  protected function createComponentFoundOrderForm(FoundOrderFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Řád založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionJoin(int $id): void {
    try {
      $this->model->join($id);
      $message = $this->localeModel->genderMessage("Vstoupil(a) jsi do řádu.");
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinOrderException $e) {
      $this->flashMessage("Nemůžeš vstoupit do řádu.");
      $this->redirect("Homepage:");
    } catch(OrderNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  public function actionLeave(): void {
    try {
      $this->model->leave();
      $message = $this->localeModel->genderMessage("Opustil(a) jsi řádu.");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveOrderException $e) {
      $this->flashMessage("Nemůžeš opustit cech.");
      $this->redirect("Homepage:");
    }
  }
  
  public function actionManage(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat řád.");
      $this->redirect("Homepage:");
    }
    $this->template->order =  $this->model->getUserOrder();
      $this->template->canUpgrade = $this->model->canUpgrade();
  }
  
  protected function createComponentManageOrderForm(ManageOrderFormFactory $factory): Form {
    /** @var \Nexendrie\Orm\Order $order */
    $order = $this->model->getUserOrder();
    $form = $factory->create($order->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  public function handleUpgrade(): void {
    try {
      $this->model->upgrade();
      $this->flashMessage("Řád vylepšen.");
      $this->redirect("manage");
    } catch(CannotUpgradeOrderException $e) {
      $this->flashMessage("Nemůžeš vylepšit řád.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
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
  
  public function handlePromote(int $user): void {
    try {
      $this->model->promote($user);
      $this->flashMessage("Povýšen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException $e) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException $e) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourOrderException $e) {
      $this->flashMessage("Uživatel není ve tvém řádu.");
      $this->redirect("Homepage:");
    } catch(CannotPromoteMemberException $e) {
      $this->flashMessage("Uživatel nemůže být povýšen.");
      $this->redirect("members");
    }
  }
  
  public function handleDemote(int $user): void {
    try {
      $this->model->demote($user);
      $this->flashMessage("Degradován(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException $e) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException $e) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourOrderException $e) {
      $this->flashMessage("Uživatel není ve tvém řádu.");
      $this->redirect("Homepage:");
    } catch(CannotDemoteMemberException $e) {
      $this->flashMessage("Uživatel nemůže být degradován.");
      $this->redirect("members");
    }
  }
  
  public function handleKick(int $user): void {
    try {
      $this->model->kick($user);
      $this->flashMessage("Vyloučen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException $e) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException $e) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourOrderException $e) {
      $this->flashMessage("Uživatel není ve tvém řádu.");
      $this->redirect("Homepage:");
    } catch(CannotKickMemberException $e) {
      $this->flashMessage("Uživatel nemůže být vyloučen.");
      $this->redirect("members");
    }
  }
}
?>