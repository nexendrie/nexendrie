<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nexendrie\Orm\House as HouseEntity;

require __DIR__ . "/../../bootstrap.php";

final class HouseTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var House */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(House::class);
  }
  
  public function testGetUserHouse() {
    $house = $this->model->getUserHouse(3);
    Assert::type(HouseEntity::class, $house);
    Assert::null($this->model->getUserHouse(1));
    Assert::null($this->model->getUserHouse(50));
  }
  
  public function testBuyHouse() {
    Assert::exception(function() {
      $this->model->buyHouse();
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->buyHouse();
    }, CannotBuyMoreHousesException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->buyHouse();
    }, CannotBuyHouseException::class);
  }
  
  public function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgrade());
    $this->login("jakub");
    Assert::false($this->model->canUpgrade());
  }
  
  public function testUpgrade() {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->upgrade();
    }, CannotUpgradeHouseException::class);
  }
  
  public function testCanRepair() {
    Assert::exception(function() {
      $this->model->canRepair();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canRepair());
    $this->login("jakub");
    Assert::false($this->model->canRepair());
  }
  
  public function testRepair() {
    Assert::exception(function() {
      $this->model->repair();
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->repair();
    }, CannotRepairHouseException::class);
  }
  
  public function testCanUpgradeBrewery() {
    Assert::exception(function() {
      $this->model->canUpgradeBrewery();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgradeBrewery());
    $this->login("jakub");
    Assert::false($this->model->canUpgradeBrewery());
  }
  
  public function testUpgradeBrewery() {
    Assert::exception(function() {
      $this->model->upgradeBrewery();
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->upgradeBrewery();
    }, CannotUpgradeBreweryException::class);
  }
  
  public function testCanProduceBeer() {
    Assert::exception(function() {
      $this->model->canProduceBeer();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canProduceBeer());
    $this->login("jakub");
    Assert::type("bool", $this->model->canProduceBeer());
  }
  
  public function testProduceBeer() {
    Assert::exception(function() {
      $this->model->produceBeer();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->produceBeer();
    }, CannotProduceBeerException::class);
  }
}

$test = new HouseTest;
$test->run();
?>