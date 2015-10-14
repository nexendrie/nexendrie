<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Components\ShopControlFactory,
    Nexendrie\Components\MountsMarketControlFactory,
    Nexendrie\Components\MountsMarketControl;

/**
 * Presenter Market
 *
 * @author Jakub Konečný
 */
class MarketPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Market @autowire */
  protected $model;
  
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
   * @param ShopControlFactory $factory
   * @return \Nette\Application\UI\Multiplier
   */
  protected function createComponentShop(ShopControlFactory $factory) {
    return new \Nette\Application\UI\Multiplier(function ($id) use ($factory) {
      $shop = $factory->create();
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
  
  /**
   * @return void
   */
  function renderMounts() {
    $this->requiresLogin();
  }
  
  /**
   * @param MountsMarketControlFactory $factory
   * @return MountsMarketControl
   */
  protected function createComponentMountsMarket(MountsMarketControlFactory $factory) {
    return $factory->create();
  }
}
?>