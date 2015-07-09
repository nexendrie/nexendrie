<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Market
 *
 * @author Jakub Konečný
 */
class MarketPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Market */
  protected $model;
  
  /**
   * @param \Nexendrie\Model\Market $model
   */
  function __construct(\Nexendrie\Model\Market $model) {
    $this->model = $model;
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->shops = $this->model->listOfShops();
  }
  
  /**
   * @param int $id Shop's id
   * @return void
   */
  function actionShop($id) {
    if(!$this->model->exists($id)) $this->forward("notfound");
    $this->template->shopId = $id;
  }
  
  /**
   * @return \Nette\Application\UI\Multiplier
   */
  function createComponentShop() {
    $p = $this;
    return new \Nette\Application\UI\Multiplier(function ($id) use ($p) {
      $shop = $p->context->getService("shop");
      $shop->id = $id;
      return $shop;
    });
  }
  
  /**
   * @param int $id Item's id
   * @return void
   */
  function actionBuy($id) {
    $this->requiresLogin();
  }
}
?>