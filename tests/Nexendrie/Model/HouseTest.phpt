<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nexendrie\Orm\House as HouseEntity;

require __DIR__ . "/../../bootstrap.php";

final class HouseTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var House */
  protected $model;
  
  protected function setUp() {
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
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->buyHouse();
    }, CannotBuyMoreHousesException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->buyHouse();
    }, CannotBuyHouseException::class);
    $this->login("premysl");
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->buyHouse();
      });
    }, InsufficientFundsException::class);
  }
  
  public function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgrade());
    $this->login("Jakub");
    $this->modifyHouse(["luxuryLevel" => 1], function() {
      Assert::true($this->model->canUpgrade());
    });
    Assert::false($this->model->canUpgrade());
  }
  
  public function testUpgrade() {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
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
    $this->login("Jakub");
    Assert::false($this->model->canRepair());
  }
  
  public function testRepair() {
    Assert::exception(function() {
      $this->model->repair();
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
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
    $this->login("Jakub");
    $this->modifyHouse(["breweryLevel" => 1], function() {
      Assert::true($this->model->canUpgradeBrewery());
    });
    Assert::false($this->model->canUpgradeBrewery());
  }
  
  public function testUpgradeBrewery() {
    Assert::exception(function() {
      $this->model->upgradeBrewery();
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
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
    $this->login("Jakub");
    $this->modifyHouse(["breweryLevel" => 0], function() {
      Assert::false($this->model->canProduceBeer());
    });
    $this->modifyHouse(["hp" => 29], function() {
      Assert::false($this->model->canProduceBeer());
    });
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

$test = new HouseTest();
$test->run();
?>