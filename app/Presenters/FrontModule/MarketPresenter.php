<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IShopControlFactory,
    Nexendrie\Components\IMountsMarketControlFactory,
    Nexendrie\Components\MountsMarketControl,
    Nexendrie\Components\ITownsMarketControlFactory,
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
  function renderDefault(): void {
    $this->template->shops = $this->model->listOfShops();
  }
  
  /**
   * @param int $id Shop's id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionShop(int $id): void {
    if(!$this->model->exists($id)) {
      throw new \Nette\Application\BadRequestException;
    }
    $this->template->shopId = $id;
  }
  
  /**
   * @param IShopControlFactory $factory
   * @return \Nette\Application\UI\Multiplier
   */
  protected function createComponentShop(IShopControlFactory $factory): \Nette\Application\UI\Multiplier {
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
  function actionBuy(int $id): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @return void
   */
  function renderMounts(): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @param IMountsMarketControlFactory $factory
   * @return MountsMarketControl
   */
  protected function createComponentMountsMarket(IMountsMarketControlFactory $factory): MountsMarketControl {
    return $factory->create();
  }
  
  /**
   * @return void
   */
  function renderTowns(): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @param ITownsMarketControlFactory $factory
   * @return TownsMarketControl
   */
  protected function createComponentTownsMarket(ITownsMarketControlFactory $factory): TownsMarketControl {
    return $factory->create();
  }
}
?>