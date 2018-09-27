<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Monastery as MonasteryEntity;

require __DIR__ . "/../../bootstrap.php";

final class MonasteryTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Monastery */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Monastery::class);
  }
  
  public function testListOfMonasteries() {
    $result = $this->model->listOfMonasteries();
    Assert::type(ICollection::class, $result);
    Assert::type(MonasteryEntity::class, $result->fetch());
  }
  
  public function testGet() {
    $monastery = $this->model->get(1);
    Assert::type(MonasteryEntity::class, $monastery);
    Assert::exception(function() {
      $this->model->get(50);
    }, MonasteryNotFoundException::class);
  }
  
  public function testGetByUser() {
    $monastery = $this->model->getByUser(2);
    Assert::type(MonasteryEntity::class, $monastery);
    Assert::exception(function() {
      $this->model->getByUser(1);
    }, NotInMonasteryException::class);
    Assert::exception(function() {
      $this->model->getByUser(50);
    }, UserNotFoundException::class);
  }
  
  public function testCanJoin() {
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
  
  public function testJoin() {
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
    $this->login("bozena");
    Assert::exception(function() {
      $this->model->join(1);
    }, CannotJoinOwnMonasteryException::class);
    $this->login("svetlana");
    $user = $this->getUser();
    $stats = [
      "monastery", "group", "order", "orderRank", "town"
    ];
    $this->preserveStats($stats, function() use($user) {
      $this->model->join(1);
      Assert::type(\Nexendrie\Orm\Monastery::class, $user->monastery);
      Assert::null($user->order);
      Assert::null($user->orderRank);
      Assert::same(55, $user->group->level);
    });
  }
  
  public function testCanPray() {
    Assert::exception(function() {
      $this->model->canPray();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canPray());
    $this->login("Rahym");
    Assert::type("bool", $this->model->canPray());
  }
  
  public function testPray() {
    Assert::exception(function() {
      $this->model->pray();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->pray();
    }, CannotPrayException::class);
  }
  
  public function testCanLeave() {
    Assert::exception(function() {
      $this->model->canLeave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canLeave());
    $this->login("Rahym");
    Assert::false($this->model->canLeave());
    $this->login("bozena");
    Assert::true($this->model->canLeave());
  }
  
  public function testLeave() {
    Assert::exception(function() {
      $this->model->leave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->leave();
    }, CannotLeaveMonasteryException::class);
    $this->login("bozena");
    $user = $this->getUser();
    $this->preserveStats(["monastery", "group"], function() use($user) {
      $this->model->leave();
      Assert::null($user->monastery);
      Assert::same(50, $user->group->level);
    });
  }
  
  public function testCanBuild() {
    Assert::exception(function() {
      $this->model->canBuild();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canBuild());
    $this->login("Rahym");
    Assert::false($this->model->canBuild());
  }
  
  public function testBuild() {
    Assert::exception(function() {
      $this->model->build("abc");
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->build("abc");
    }, CannotBuildMonasteryException::class);
    $this->login("bozena");
    Assert::exception(function() {
      $this->modifyUser(["group" => 4], function() {
        $monastery = $this->model->get(1);
        $this->model->build($monastery->name);
      });
    }, MonasteryNameInUseException::class);
    Assert::exception(function() {
      $this->modifyUser(["group" => 4, "money" => 1], function() {
        $this->model->build("abc");
      });
    }, InsufficientFundsException::class);
  }
  
  public function testDonate() {
    Assert::exception(function() {
      $this->model->donate(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->donate(1);
    }, NotInMonasteryException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      /** @var int $money */
      $money = $this->getUserStat("money");
      $this->model->donate($money + 1);
    }, InsufficientFundsException::class);
  }
  
  public function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, MonasteryNotFoundException::class);
    $monastery1 = $this->model->get(1);
    $name = $monastery1->name;
    $money = $monastery1->money;
    $monastery2 = $this->model->get(2);
    Assert::exception(function() use($monastery2) {
      $this->model->edit(1, ["name" => $monastery2->name]);
    }, MonasteryNameInUseException::class);
    $this->model->edit(1, [
      "name" => "abc", "money" => 1
    ]);
    Assert::same("abc", $monastery1->name);
    Assert::same($money, $monastery1->money);
    $this->model->edit(1, [
      "name" => $name
    ]);
  }
  
  public function testHighClerics() {
    $result = $this->model->highClerics(2);
    Assert::type("array", $result);
    Assert::count(1, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  public function testCanManage() {
    Assert::exception(function() {
      $this->model->canManage();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canManage());
    $this->login("Rahym");
    Assert::true($this->model->canManage());
    $this->login("bozena");
    Assert::false($this->model->canManage());
  }
  
  public function testPrayerLife() {
    Assert::same(0, $this->model->prayerLife());
    $this->login();
    Assert::same(0, $this->model->prayerLife());
    $this->login("Rahym");
    $result = $this->model->prayerLife();
    Assert::type("int", $result);
    Assert::true($result > 0);
  }
  
  public function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgrade());
    $this->login("Rahym");
    Assert::false($this->model->canUpgrade());
    $this->login("bozena");
    Assert::false($this->model->canUpgrade());
  }
  
  public function testUpgrade() {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgrade();
    }, CannotUpgradeMonasteryException::class);
  }
  
  public function testCanUpgradeLibrary() {
    Assert::exception(function() {
      $this->model->canUpgradeLibrary();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgradeLibrary());
    $this->login("Rahym");
    Assert::true($this->model->canUpgradeLibrary());
    $this->login("bozena");
    Assert::false($this->model->canUpgradeLibrary());
  }
  
  public function testUpgradeLibrary() {
    Assert::exception(function() {
      $this->model->upgradeLibrary();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgradeLibrary();
    }, CannotUpgradeMonasteryException::class);
  }
  
  public function testCanRepair() {
    Assert::exception(function() {
      $this->model->canRepair();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canRepair());
    $this->login("Rahym");
    Assert::false($this->model->canRepair());
    $this->login("bozena");
    Assert::false($this->model->canRepair());
  }
  
  public function testRepair() {
    Assert::exception(function() {
      $this->model->repair();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->repair();
    }, CannotRepairMonasteryException::class);
  }
}

$test = new MonasteryTest();
$test->run();
?>