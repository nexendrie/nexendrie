<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Shop as ShopEntity,
    Nexendrie\Orm\Item as ItemEntity;

require __DIR__ . "/../../bootstrap.php";

class MarketTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Market */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Market::class);
  }
  
  function testListOfShops() {
    $result = $this->model->listOfShops();
    Assert::type(ICollection::class, $result);
    Assert::type(ShopEntity::class, $result->fetch());
  }
  
  function testListOfItems() {
    $result = $this->model->listOfItems();
    Assert::type(ICollection::class, $result);
    Assert::type(ItemEntity::class, $result->fetch());
  }
  
  function testExists() {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
  
  function testGetShop() {
    $shop = $this->model->getShop(1);
    Assert::type(ShopEntity::class, $shop);
    Assert::exception(function() {
      $this->model->getShop(50);
    }, ShopNotFoundException::class);
  }
  
  function testEditShop() {
    $shop = $this->model->getShop(1);
    $name = $shop->name;
    $this->model->editShop($shop->id, ["name" => "abc"]);
    Assert::same("abc", $shop->name);
    $this->model->editShop($shop->id, ["name" => $name]);
  }
  
  function testGetItem() {
    $item = $this->model->getItem(1);
    Assert::type(ItemEntity::class, $item);
    Assert::exception(function() {
      $this->model->getItem(50);
    }, ItemNotFoundException::class);
  }
  
  function testEditItem() {
    $item = $this->model->getItem(1);
    $name = $item->name;
    $this->model->editItem($item->id, ["name" => "abc"]);
    Assert::same("abc", $item->name);
    $this->model->editItem($item->id, ["name" => $name]);
  }
}

$test = new MarketTest;
$test->run();
?>