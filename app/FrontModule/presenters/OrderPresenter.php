<?php
namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Order
 *
 * @author Jakub Konečný
 */
class OrderPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Order @autowire  */
  protected $model;
  
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
  function renderList() {
    $this->template->orders = $this->model->listOfOrders();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    
  }
}
?>