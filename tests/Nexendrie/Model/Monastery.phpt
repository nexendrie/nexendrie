<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Monastery as MonasteryEntity;

require __DIR__ . "/../../bootstrap.php";

class MonasteryTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \TUserControl;
  
  /** @var Monastery */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Monastery::class);
  }
  
  function testGetBuildingPrice() {
    Assert::type("int", $this->model->buildingPrice);
  }
  
  function testListOfMonasteries() {
    $result = $this->model->listOfMonasteries();
    Assert::type(ICollection::class, $result);
    Assert::type(MonasteryEntity::class, $result->fetch());
  }
  
  function testGet() {
    $monastery = $this->model->get(1);
    Assert::type(MonasteryEntity::class, $monastery);
    Assert::exception(function() {
      $this->model->get(50);
    }, MonasteryNotFoundException::class);
  }
  
  function testGetByUser() {
    $monastery = $this->model->getByUser(2);
    Assert::type(MonasteryEntity::class, $monastery);
    Assert::exception(function() {
      $this->model->getByUser(1);
    }, NotInMonasteryException::class);
    Assert::exception(function() {
      $this->model->getByUser(50);
    }, UserNotFoundException::class);
  }
  
  function testCanJoin() {
    Assert::false($this->model->canJoin());
    $this->login("jakub");
    Assert::false($this->model->canJoin());
    $this->login("kazimira");
    Assert::true($this->model->canJoin());
    $this->login();
    Assert::false($this->model->canJoin());
    $this->login("svetlana");
    Assert::true($this->model->canJoin());
    $this->login("Rahym");
    Assert::false($this->model->canJoin());
  }
  
  function testJoin() {
    Assert::exception(function() {
      $this->model->join(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->join(1);
    }, CannotJoinMonasteryException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->join(50);
    }, MonasteryNotFoundException::class);
  }
  
  function testCanPray() {
    Assert::exception(function() {
      $this->model->canPray();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canPray());
    $this->login("Rahym");
    Assert::type("bool", $this->model->canPray());
  }
  
  function testCanLeave() {
    Assert::exception(function() {
      $this->model->canLeave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canLeave());
    $this->login("Rahym");
    Assert::false($this->model->canLeave());
  }
  
  function testCanBuild() {
    Assert::exception(function() {
      $this->model->canBuild();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canBuild());
    $this->login("Rahym");
    Assert::false($this->model->canBuild());
  }
  
  function testHighClerics() {
    $result = $this->model->highClerics(2);
    Assert::type("array", $result);
    Assert::count(1, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  function testCanManage() {
    Assert::exception(function() {
      $this->model->canManage();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canManage());
    $this->login("Rahym");
    Assert::true($this->model->canManage());
  }
  
  function testPrayerLife() {
    Assert::same(0, $this->model->prayerLife());
    $this->login();
    Assert::same(0, $this->model->prayerLife());
    $this->login("Rahym");
    $result = $this->model->prayerLife();
    Assert::type("int", $result);
    Assert::true($result > 0);
  }
  
  function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgrade());
    $this->login("Rahym");
    Assert::type("bool", $this->model->canUpgrade());
  }
  
  function testCanRepair() {
    Assert::exception(function() {
      $this->model->canRepair();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canRepair());
    $this->login("Rahym");
    Assert::type("bool", $this->model->canRepair());
  }
}

$test = new MonasteryTest;
$test->run();
?>