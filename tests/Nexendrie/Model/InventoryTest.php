<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Orm\Town as TownEntity;

require __DIR__ . "/../../bootstrap.php";

final class InventoryTest extends \Tester\TestCase {
  use TUserControl;

  protected Inventory $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Inventory::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testPossessions(): void {
    Assert::exception(function() {
      $this->model->possessions();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->possessions();
    Assert::type("array", $result);
    Assert::type("int", $result["money"]);
    Assert::type(ICollection::class, $result["items"]);
    Assert::type(OneHasMany::class, $result["towns"]);
    Assert::type(TownEntity::class, $result["towns"]->getIterator()->fetch());
  }
  
  public function testEquipment(): void {
    Assert::exception(function() {
      $this->model->equipment();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->equipment();
    Assert::type(ICollection::class, $result);
  }
  
  public function testPotions(): void {
    Assert::exception(function() {
      $this->model->potions();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->potions();
    Assert::type(ICollection::class, $result);
  }
  
  public function testIntimacyBoosters(): void {
    Assert::exception(function() {
      $this->model->intimacyBoosters();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->intimacyBoosters();
    Assert::type(ICollection::class, $result);
  }
  
  public function testEquipItem(): void {
    Assert::exception(function() {
      $this->model->equipItem(5000);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->equipItem(5000);
    }, ItemNotFoundException::class);
    $this->login("Jakub");
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
  
  public function testUnequipItem(): void {
    Assert::exception(function() {
      $this->model->unequipItem(5000);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->unequipItem(5000);
    }, ItemNotFoundException::class);
    $this->login("Jakub");
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
  
  public function testDrinkPotion(): void {
    Assert::exception(function() {
      $this->model->drinkPotion(5000);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->drinkPotion(5000);
    }, ItemNotFoundException::class);
    $this->login("Jakub");
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
  
  public function testBoostIntimacy(): void {
    Assert::exception(function() {
      $this->model->boostIntimacy(5000);
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->boostIntimacy(5000);
    }, NotMarriedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->boostIntimacy(5000);
    }, ItemNotFoundException::class);
    $this->login("Světlana");
    Assert::exception(function() {
      $this->model->boostIntimacy(19);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->boostIntimacy(3);
    }, ItemNotUsableException::class);
  }
  
  public function testSellItem(): void {
    Assert::exception(function() {
      $this->model->sellItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->sellItem(50);
    }, ItemNotFoundException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->sellItem(3);
    }, ItemNotOwnedException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->sellItem(19);
    }, ItemNotForSaleException::class);
  }
  
  public function testUpgradeItem(): void {
    Assert::exception(function() {
      $this->model->upgradeItem(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgradeItem(50);
    }, ItemNotFoundException::class);
    $this->login("Jakub");
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