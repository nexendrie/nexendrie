<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Castle as CastleEntity;

require __DIR__ . "/../../bootstrap.php";

class CastleTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Castle */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Castle::class);
  }
  
  function testGetBuildingPrice() {
    Assert::type("int", $this->model->buildingPrice);
  }
  
  function testListOfCastles() {
    $result = $this->model->listOfCastles();
    Assert::type(ICollection::class, $result);
    Assert::type(CastleEntity::class, $result->fetch());
  }
  
  function testGetCastle() {
    $castle = $this->model->getCastle(1);
    Assert::type(CastleEntity::class, $castle);
    Assert::exception(function() {
      $this->model->getCastle(50);
    }, CastleNotFoundException::class);
  }
  
  function testEditCastle() {
    Assert::exception(function() {
      $this->model->editCastle(50, []);
    }, CastleNotFoundException::class);
  }
  
  function testBuild() {
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
  
  function testGetUserCastle() {
    $castle = $this->model->getUserCastle(1);
    Assert::type(CastleEntity::class, $castle);
    Assert::null($this->model->getUserCastle(2));
    Assert::null($this->model->getUserCastle(50));
  }
  
  function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->canUpgrade());
  }
  
  function testCanRepair() {
    Assert::exception(function() {
      $this->model->canRepair();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->canRepair());
  }
}

$test = new CastleTest;
$test->run();
?>