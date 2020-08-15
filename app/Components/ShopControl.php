<?php
declare(strict_types=1);

namespace Nexendrie\Components;

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
  protected \Nexendrie\Model\Market $model;
  protected \Nexendrie\Model\Events $eventsModel;
  protected \Nexendrie\Orm\Model $orm;
  protected \Nette\Security\User $user;
  protected ShopEntity $shop;
  protected int $id;
  
  public function __construct(\Nexendrie\Model\Market $model, \Nexendrie\Model\Events $eventsModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->model = $model;
    $this->eventsModel = $eventsModel;
    $this->orm = $orm;
    $this->user = $user;
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