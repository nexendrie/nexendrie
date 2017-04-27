<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nextras\Orm\Relationships\OneHasMany,
    Nexendrie\Orm\Town as TownEntity;

require __DIR__ . "/../../bootstrap.php";

class InventoryTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Inventory */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Inventory::class);
  }
  
  function testPossessions() {
    Assert::exception(function() {
      $this->model->possessions();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->possessions();
    Assert::type("array", $result);
    Assert::type("string", $result["money"]);
    Assert::contains("groš", $result["money"]);
    Assert::type(ICollection::class, $result["items"]);
    Assert::type(OneHasMany::class, $result["towns"]);
    Assert::type(TownEntity::class, $result["towns"]->get()->fetch());
  }
  
  function testEquipment() {
    Assert::exception(function() {
      $this->model->equipment();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->equipment();
    Assert::type(ICollection::class, $result);
  }
  
  function testPotions() {
    Assert::exception(function() {
      $this->model->potions();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->potions();
    Assert::type(ICollection::class, $result);
  }
  
  function testIntimacyBoosters() {
    Assert::exception(function() {
      $this->model->intimacyBoosters();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->intimacyBoosters();
    Assert::type(ICollection::class, $result);
  }
  
  function testEquipItem() {
    Assert::exception(function() {
      $this->model->equipItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->equipItem(50);
    }, ItemNotFoundException::class);
  }
  
  function testUnequipItem() {
    Assert::exception(function() {
      $this->model->unequipItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->unequipItem(50);
    }, ItemNotFoundException::class);
  }
  
  function testDrinkPotion() {
    Assert::exception(function() {
      $this->model->drinkPotion(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->drinkPotion(50);
    }, ItemNotFoundException::class);
  }
  
  function testBoostIntimacy() {
    Assert::exception(function() {
      $this->model->boostIntimacy(1);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->boostIntimacy(50);
    }, NotMarriedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->boostIntimacy(50);
    }, ItemNotFoundException::class);
  }
  
  function testSellItem() {
    Assert::exception(function() {
      $this->model->sellItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->sellItem(50);
    }, ItemNotFoundException::class);
  }
  
  function testUpgradetem() {
    Assert::exception(function() {
      $this->model->upgradeItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgradeItem(50);
    }, ItemNotFoundException::class);
  }
}

$test = new InventoryTest;
$test->run();
?>