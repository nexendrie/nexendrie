<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Castle as CastleEntity;

require __DIR__ . "/../../bootstrap.php";

final class CastleTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Castle */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Castle::class);
  }
  
  public function testGetBuildingPrice() {
    Assert::type("int", $this->model->buildingPrice);
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
    $this->login();
    Assert::type("bool", $this->model->canUpgrade());
  }
  
  public function testCanRepair() {
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