<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Shop as ShopEntity;
use Nexendrie\Orm\Item as ItemEntity;

require __DIR__ . "/../../bootstrap.php";

final class MarketTest extends \Tester\TestCase {
  use TUserControl;

  protected Market $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Market::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testListOfShops(): void {
    $result = $this->model->listOfShops();
    Assert::type(ICollection::class, $result);
    Assert::type(ShopEntity::class, $result->fetch());
  }
  
  public function testListOfItems(): void {
    $result = $this->model->listOfItems();
    Assert::type(ICollection::class, $result);
    Assert::type(ItemEntity::class, $result->fetch());
  }
  
  public function testExists(): void {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
  
  public function testGetShop(): void {
    $shop = $this->model->getShop(1);
    Assert::type(ShopEntity::class, $shop);
    Assert::exception(function() {
      $this->model->getShop(50);
    }, ShopNotFoundException::class);
  }
  
  public function testEditShop(): void {
    $shop = $this->model->getShop(2);
    $name = $shop->name;
    $this->model->editShop($shop->id, ["name" => "abc"]);
    Assert::same("abc", $shop->name);
    $this->model->editShop($shop->id, ["name" => $name]);
    Assert::notSame("abc", $shop->name);
    Assert::exception(function() {
      $this->model->editShop(5000, []);
    }, ShopNotFoundException::class);
  }
  
  public function testGetItem(): void {
    $item = $this->model->getItem(1);
    Assert::type(ItemEntity::class, $item);
    Assert::exception(function() {
      $this->model->getItem(50);
    }, ItemNotFoundException::class);
  }
  
  public function testEditItem(): void {
    $item = $this->model->getItem(1);
    $name = $item->name;
    $this->model->editItem($item->id, ["name" => "abc"]);
    Assert::same("abc", $item->name);
    $this->model->editItem($item->id, ["name" => $name]);
    Assert::exception(function() {
      $this->model->editItem(5000, []);
    }, ItemNotFoundException::class);
  }
  
  public function testBuy(): void {
    Assert::exception(function() {
      $this->model->buy(50, 50);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->buy(50, 50);
    }, ItemNotFoundException::class);
    Assert::exception(function() {
      $this->model->buy(1, 50);
    }, WrongShopException::class);
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->buy(1, 2);
      });
    }, InsufficientFundsException::class);
  }
}

$test = new MarketTest();
$test->run();
?>