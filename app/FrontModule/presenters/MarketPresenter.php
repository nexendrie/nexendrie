<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\ShopControlFactory,
    Nexendrie\Components\MountsMarketControlFactory,
    Nexendrie\Components\MountsMarketControl,
    Nexendrie\Components\TownsMarketControlFactory,
    Nexendrie\Components\TownsMarketControl;

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
  protected function startup() {
    parent::startup();
    $this->mustNotBeTavelling();
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
    if(!$this->model->exists($id)) throw new \Nette\Application\BadRequestException;
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
    $this->mustNotBeBanned();
  }
  
  /**
   * @return void
   */
  function renderMounts() {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @param MountsMarketControlFactory $factory
   * @return MountsMarketControl
   */
  protected function createComponentMountsMarket(MountsMarketControlFactory $factory) {
    return $factory->create();
  }
  
  /**
   * @return void
   */
  function renderTowns() {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @param TownsMarketControlFactory $factory
   * @return TownsMarketControl
   */
  protected function createComponentTownsMarket(TownsMarketControlFactory $factory) {
    return $factory->create();
  }
}
?>