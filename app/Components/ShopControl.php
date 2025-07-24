<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\Events;
use Nexendrie\Model\Market;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Shop as ShopEntity;
use Nexendrie\Model\ShopNotFoundException;
use Nexendrie\Model\ItemNotFoundException;
use Nexendrie\Model\WrongShopException;
use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\InsufficientFundsException;

/**
 * Shop Control
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 * @property-write int $id
 */
final class ShopControl extends \Nette\Application\UI\Control {
  private ShopEntity $shop;
  private int $id;
  
  public function __construct(private readonly Market $model, private readonly Events $eventsModel, private readonly ORM $orm) {
  }
  
  /**
   * @throws ShopNotFoundException
   */
  public function getShop(): ShopEntity {
    if(!isset($this->shop)) {
      $shop = $this->orm->shops->getById($this->id);
      if($shop === null) {
        throw new ShopNotFoundException("Specified shop does not exist.");
      }
      $this->shop = $shop;
    }
    return $this->shop;
  }
  
  protected function setId(int $id): void {
    try {
      $this->id = $id;
      $this->getShop();
    } catch(ShopNotFoundException $e) {
      throw $e;
    }
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/shop.latte");
    $this->template->shop = $this->getShop();
    $this->template->discount = $this->eventsModel->getShoppingDiscount();
    $this->template->render();
  }
  
  public function handleBuy(int $item): void {
    try {
      $this->model->buy($item, $this->shop->id);
      $this->presenter->flashMessage("Věc koupena.");
    } catch(AuthenticationNeededException $e) {
      $this->presenter->flashMessage("Pro nákup musíš být přihlášený.");
    } catch(ItemNotFoundException $e) {
      $this->presenter->flashMessage("Zadaná věc neexistuje.");
    } catch(WrongShopException $e) {
      $this->presenter->flashMessage("Zadaná věc není v aktuálním obchodě.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz na zakoupení této věci.");
    }
    $this->presenter->redirect(":Front:Market:shop", ["id" => $this->shop->id]);
  }
}
?>