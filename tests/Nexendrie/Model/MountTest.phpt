<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Orm\MountType as MountTypeEntity;

require __DIR__ . "/../../bootstrap.php";

class MountTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Mount */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Mount::class);
  }
  
  public function testGet() {
    $mount = $this->model->get(1);
    Assert::type(MountEntity::class, $mount);
    Assert::exception(function() {
      $this->model->get(50);
    }, MountNotFoundException::class);
  }
  
  public function testListOfMounts() {
    $result1 = $this->model->listOfMounts();
    Assert::type(ICollection::class, $result1);
    Assert::count(12, $result1);
    $result2 = $this->model->listOfMounts(0);
    Assert::type(ICollection::class, $result2);
    Assert::count(7, $result2);
    /** @var MountEntity $mount */
    $mount = $result2->fetch();
    Assert::type(MountEntity::class, $mount);
    Assert::same(0, $mount->owner->id);
  }
  
  public function testMountsOnSale() {
    $result = $this->model->mountsOnSale();
    Assert::type(ICollection::class, $result);
    /** @var MountEntity $mount */
    $mount = $result->fetch();
    Assert::type(MountEntity::class, $mount);
    Assert::true($mount->onMarket);
  }
  
  public function testListOfMountTypes() {
    $result = $this->model->listOfMountTypes();
    Assert::type(ICollection::class, $result);
    Assert::count(5, $result);
    Assert::type(MountTypeEntity::class, $result->fetch());
  }
  
  public function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, MountNotFoundException::class);
  }
  
  public function testBuy() {
    Assert::exception(function() {
      $this->model->buy(1);
    }, AuthenticationNeededException::class);
    $this->login("system");
    Assert::exception(function() {
      $this->model->buy(50);
    }, MountNotFoundException::class);
    Assert::exception(function() {
      $this->model->buy(2);
    }, MountNotOnSaleException::class);
    Assert::exception(function() {
      $this->model->buy(1);
    }, CannotBuyOwnMountException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->buy(9);
    }, InsufficientLevelForMountException::class);
  }
}

$test = new MountTest;
$test->run();
?>