<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CannotBuyMoreHousesException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * Presenter House
 *
 * @author Jakub Konečný
 */
class HousePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\House @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeTavelling();
    if($this->profileModel->getPath() != "city") {
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function actionBuy() {
    try {
      $this->model->buyHouse();
      $this->flashMessage("Dům zakoupen.");
      $this->redirect("default");
    } catch(CannotBuyMoreHousesException $e) {
      $this->flashMessage("Už vlastníš dům.");
      $this->redirect("default");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nemáš dostatek peněz.");
      $this->redirect("Homepage:");
    }
  }
}
?>