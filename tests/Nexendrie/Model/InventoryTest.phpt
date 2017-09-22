<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nextras\Orm\Relationships\OneHasMany,
    Nexendrie\Orm\Town as TownEntity;

require __DIR__ . "/../../bootstrap.php";

final class InventoryTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Inventory */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Inventory::class);
  }
  
  public function testPossessions() {
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
  
  public function testEquipment() {
    Assert::exception(function() {
      $this->model->equipment();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->equipment();
    Assert::type(ICollection::class, $result);
  }
  
  public function testPotions() {
    Assert::exception(function() {
      $this->model->potions();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->potions();
    Assert::type(ICollection::class, $result);
  }
  
  public function testIntimacyBoosters() {
    Assert::exception(function() {
      $this->model->intimacyBoosters();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->intimacyBoosters();
    Assert::type(ICollection::class, $result);
  }
  
  public function testEquipItem() {
    Assert::exception(function() {
      $this->model->equipItem(5000);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->equipItem(5000);
    }, ItemNotFoundException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->equipItem(3);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->equipItem(3);
    }, ItemNotEquipableException::class);
    Assert::exception(function() {
      $this->model->equipItem(1);
    }, ItemAlreadyWornException::class);
  }
  
  public function testUnequipItem() {
    Assert::exception(function() {
      $this->model->unequipItem(5000);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->unequipItem(5000);
    }, ItemNotFoundException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->unequipItem(3);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->unequipItem(3);
    }, ItemNotEquipableException::class);
    Assert::exception(function() {
      $this->model->unequipItem(23);
    }, ItemNotWornException::class);
  }
  
  public function testDrinkPotion() {
    Assert::exception(function() {
      $this->model->drinkPotion(5000);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->drinkPotion(5000);
    }, ItemNotFoundException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->drinkPotion(3);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->drinkPotion(1);
    }, ItemNotDrinkableException::class);
    Assert::exception(function() {
      $this->model->drinkPotion(3);
    }, HealingNotNeededException::class);
  }
  
  public function testBoostIntimacy() {
    Assert::exception(function() {
      $this->model->boostIntimacy(5000);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->boostIntimacy(5000);
    }, NotMarriedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->boostIntimacy(5000);
    }, ItemNotFoundException::class);
    $this->login("svetlana");
    Assert::exception(function() {
      $this->model->boostIntimacy(19);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->boostIntimacy(3);
    }, ItemNotUsableException::class);
  }
  
  public function testSellItem() {
    Assert::exception(function() {
      $this->model->sellItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->sellItem(50);
    }, ItemNotFoundException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->sellItem(3);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->sellItem(19);
    }, ItemNotForSaleException::class);
  }
  
  public function testUpgradeItem() {
    Assert::exception(function() {
      $this->model->upgradeItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgradeItem(50);
    }, ItemNotFoundException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->upgradeItem(3);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgradeItem(3);
    }, ItemNotUpgradableException::class);
    Assert::exception(function() {
      $this->model->upgradeItem(1);
    }, ItemMaxLevelReachedException::class);
    Assert::exception(function() {
      $this->modifyUser(["money" => 0], function() {
        $this->model->upgradeItem(23);
      });
    }, InsufficientFundsException::class);
  }
}

$test = new InventoryTest();
$test->run();
?>