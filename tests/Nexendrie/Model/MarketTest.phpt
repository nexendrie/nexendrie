<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Shop as ShopEntity,
    Nexendrie\Orm\Item as ItemEntity;

require __DIR__ . "/../../bootstrap.php";

final class MarketTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Market */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Market::class);
  }
  
  public function testListOfShops() {
    $result = $this->model->listOfShops();
    Assert::type(ICollection::class, $result);
    Assert::type(ShopEntity::class, $result->fetch());
  }
  
  public function testListOfItems() {
    $result = $this->model->listOfItems();
    Assert::type(ICollection::class, $result);
    Assert::type(ItemEntity::class, $result->fetch());
  }
  
  public function testExists() {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
  
  public function testGetShop() {
    $shop = $this->model->getShop(1);
    Assert::type(ShopEntity::class, $shop);
    Assert::exception(function() {
      $this->model->getShop(50);
    }, ShopNotFoundException::class);
  }
  
  public function testEditShop() {
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
  
  public function testGetItem() {
    $item = $this->model->getItem(1);
    Assert::type(ItemEntity::class, $item);
    Assert::exception(function() {
      $this->model->getItem(50);
    }, ItemNotFoundException::class);
  }
  
  public function testEditItem() {
    $item = $this->model->getItem(1);
    $name = $item->name;
    $this->model->editItem($item->id, ["name" => "abc"]);
    Assert::same("abc", $item->name);
    $this->model->editItem($item->id, ["name" => $name]);
    Assert::exception(function() {
      $this->model->editItem(5000, []);
    }, ItemNotFoundException::class);
  }
}

$test = new MarketTest;
$test->run();
?>