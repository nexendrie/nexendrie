<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\FoundOrderFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\OrderNotFoundException;

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
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->orders = $this->model->listOfOrders();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->order = $this->model->getOrder($id);
    } catch(OrderNotFoundException $e) {
      $this->forward("notfound");
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
}
?>