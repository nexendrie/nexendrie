<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IShopControlFactory;
use Nexendrie\Components\IMountsMarketControlFactory;
use Nexendrie\Components\MountsMarketControl;
use Nexendrie\Components\ITownsMarketControlFactory;
use Nexendrie\Components\ShopControl;
use Nexendrie\Components\TownsMarketControl;
use Nexendrie\Model\Market;

/**
 * Presenter Market
 *
 * @author Jakub Konečný
 */
final class MarketPresenter extends BasePresenter {
  protected bool $cachingEnabled = false;
  
  public function __construct(private readonly Market $model) {
    parent::__construct();
  }
  
  protected function startup(): void {
    parent::startup();
    $this->mustNotBeTavelling();
  }
  
  public function renderDefault(): void {
    $this->template->shops = $this->model->listOfShops();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionShop(int $id): void {
    if(!$this->model->exists($id)) {
      throw new \Nette\Application\BadRequestException();
    }
    $this->template->shopId = $id;
  }
  
  protected function createComponentShop(IShopControlFactory $factory): \Nette\Application\UI\Multiplier {
    return new \Nette\Application\UI\Multiplier(static function($id) use ($factory): ShopControl {
      $shop = $factory->create();
      $shop->id = (int) $id;
      return $shop;
    });
  }
  
  public function actionBuy(int $id): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  public function renderMounts(): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  protected function createComponentMountsMarket(IMountsMarketControlFactory $factory): MountsMarketControl {
    return $factory->create();
  }
  
  public function renderTowns(): void {
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  protected function createComponentTownsMarket(ITownsMarketControlFactory $factory): TownsMarketControl {
    return $factory->create();
  }
}
?>