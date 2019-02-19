<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Castle as CastleEntity;

require __DIR__ . "/../../bootstrap.php";

final class CastleTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Castle */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Castle::class);
  }
  
  public function testListOfCastles() {
    $result = $this->model->listOfCastles();
    Assert::type(ICollection::class, $result);
    Assert::type(CastleEntity::class, $result->fetch());
  }
  
  public function testGetCastle() {
    $castle = $this->model->getCastle(1);
    Assert::type(CastleEntity::class, $castle);
    Assert::exception(function() {
      $this->model->getCastle(50);
    }, CastleNotFoundException::class);
  }
  
  public function testEditCastle() {
    Assert::exception(function() {
      $this->model->editCastle(50, []);
    }, CastleNotFoundException::class);
    $castle1 = $this->model->getCastle(1);
    $name = $castle1->name;
    $castle2 = $this->model->getCastle(2);
    Assert::exception(function() use($castle2) {
      $this->model->editCastle(1, ["name" => $castle2->name]);
    }, CastleNameInUseException::class);
    $this->model->editCastle(1, ["name" => "abc"]);
    Assert::same("abc", $castle1->name);
    $this->model->editCastle(1, ["name" => $name]);
  }
  
  public function testBuild() {
    Assert::exception(function() {
      $this->model->build([]);
    }, AuthenticationNeededException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->build([]);
    }, CannotBuildCastleException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->build([]);
    }, CannotBuildMoreCastlesException::class);
  }
  
  public function testGetUserCastle() {
    $castle = $this->model->getUserCastle(1);
    Assert::type(CastleEntity::class, $castle);
    Assert::null($this->model->getUserCastle(2));
    Assert::null($this->model->getUserCastle(50));
  }
  
  public function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::false($this->model->canUpgrade());
    $this->login();
    Assert::false($this->model->canUpgrade());
    $this->login("Světlana");
    Assert::true($this->model->canUpgrade());
  }
  
  public function testUpgrade() {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->upgrade();
    }, CannotUpgradeCastleException::class);
    $this->login("Světlana");
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->upgrade();
      });
    }, InsufficientFundsException::class);
  }
  
  public function testCanRepair() {
    Assert::exception(function() {
      $this->model->canRepair();
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::false($this->model->canRepair());
    $this->login();
    $this->modifyCastle(["hp" => 99], function() {
      Assert::true($this->model->canRepair());
    });
    Assert::false($this->model->canRepair());
  }
  
  public function testRepair() {
    Assert::exception(function() {
      $this->model->repair();
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->repair();
    }, CannotRepairCastleException::class);
  }
}

$test = new CastleTest();
$test->run();
?>