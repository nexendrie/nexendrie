<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\FoundOrderFormFactory,
    Nexendrie\Forms\ManageOrderFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\OrderNotFoundException,
    Nexendrie\Model\CannotJoinOrderException,
    Nexendrie\Model\CannotLeaveOrderException,
    Nexendrie\Model\CannotUpgradeOrderException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Orm\User as UserEntity;

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
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $order = $this->model->getUserOrder();
    if(!$order) {
      $this->flashMessage("Nejsi v řádu.");
      $this->redirect("Homepage:");
    }
    $this->template->order = $order;
    $this->template->canLeave = $this->model->canLeave();
    $this->template->canManage = $this->model->canManage();
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->orders = $this->model->listOfOrders();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->order = $this->model->getOrder($id);
    } catch(OrderNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @return void
   */
  function actionFound() {
    if(!$this->model->canFound()) {
      $this->flashMessage("Nemůžeš založit řád.");
      $this->redirect("Homepage:");
    }
    $this->template->foundingPrice = $this->localeModel->money($this->model->foundingPrice);
  }
  
  /**
   * @param FoundOrderFormFactory $factory
   * @return Form
   */
  protected function createComponentFoundOrderForm(FoundOrderFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Řád založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionJoin($id) {
    try {
      $this->model->join($id);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Vstoupila jsi do řádu.";
      else $message = "Vstoupil jsi do řádu.";
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinOrderException $e) {
      $this->flashMessage("Nemůžeš vstoupit do řádu.");
      $this->redirect("Homepage:");
    } catch(GuildNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @return void
   */
  function actionLeave() {
    try {
      $this->model->leave();
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Opustila jsi řád.";
      else $message = "Opustil jsi řád.";
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveOrderException $e) {
      $this->flashMessage("Nemůžeš opustit cech.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
    * @return void
    */
   function actionManage() {
     if(!$this->model->canManage()) {
       $this->flashMessage("Nemůžeš spravovat řád.");
       $this->redirect("Homepage:");
     } else {
      $this->template->order =  $this->model->getUserOrder();
      $this->template->canUpgrade = $this->model->canUpgrade();
    }
   }
  
  /**
   * @param ManageOrderFormFactory $factory
   * @return Form
   */
  protected function createComponentManageOrderForm(ManageOrderFormFactory $factory) {
    $form = $factory->create($this->model->getUserOrder()->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
   
   /**
    * @return void
    */
  function handleUpgrade() {
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
}
?>