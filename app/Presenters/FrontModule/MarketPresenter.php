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
  
  protected function startup(): void {
    parent::startup();
    $this->mustNotBeTavelling();
  }
  
  function renderDefault(): void {
    $this->template->shops = $this->model->listOfShops();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  function actionShop(int $id): void {
    if(!$this->model->exists($id)) {
      throw new \Nette\Application\BadRequestException;
    }
    $this->template->shopId = $id;
  }
  
  protected function createComponentShop(IShopControlFactory $factory): \Nette\Application\UI\Multiplier {
    return new \Nette\Application\UI\Multiplier(function ($id) use ($factory) {
      $shop = $factory->create();
      $shop->id = $id;
      return $shop;
    });
  }
  
  function actionBuy(int $id): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  function renderMounts(): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  protected function createComponentMountsMarket(IMountsMarketControlFactory $factory): MountsMarketControl {
    return $factory->create();
  }
  
  function renderTowns(): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  protected function createComponentTownsMarket(ITownsMarketControlFactory $factory): TownsMarketControl {
    return $factory->create();
  }
}
?>