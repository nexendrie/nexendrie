<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Mount as MountEntity;
use Nexendrie\Orm\MountType as MountTypeEntity;

require __DIR__ . "/../../bootstrap.php";

final class MountTest extends \Tester\TestCase {
  use TUserControl;

  protected Mount $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Mount::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testGet(): void {
    $mount = $this->model->get(1);
    Assert::type(MountEntity::class, $mount);
    Assert::exception(function() {
      $this->model->get(50);
    }, MountNotFoundException::class);
  }
  
  public function testListOfMounts(): void {
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
  
  public function testMountsOnSale(): void {
    $result = $this->model->mountsOnSale();
    Assert::type(ICollection::class, $result);
    /** @var MountEntity $mount */
    $mount = $result->fetch();
    Assert::type(MountEntity::class, $mount);
    Assert::true($mount->onMarket);
  }
  
  public function testListOfMountTypes(): void {
    $result = $this->model->listOfMountTypes();
    Assert::type(ICollection::class, $result);
    Assert::count(5, $result);
    Assert::type(MountTypeEntity::class, $result->fetch());
  }
  
  public function testEdit(): void {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, MountNotFoundException::class);
    $mount = $this->model->get(1);
    $name = $mount->name;
    $this->model->edit(1, ["name" => "abc"]);
    Assert::same("abc", $mount->name);
    $this->model->edit(1, ["name" => $name]);
  }
  
  public function testBuy(): void {
    Assert::exception(function() {
      $this->model->buy(1);
    }, AuthenticationNeededException::class);
    $this->login("Vladěna");
    Assert::exception(function() {
      $this->model->buy(50);
    }, MountNotFoundException::class);
    Assert::exception(function() {
      $this->model->buy(2);
    }, MountNotOnSaleException::class);
    Assert::exception(function() {
      $this->model->buy(1);
    }, CannotBuyOwnMountException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->buy(9);
    }, InsufficientLevelForMountException::class);
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->buy(7);
      });
    }, InsufficientFundsException::class);
  }
}

$test = new MountTest();
$test->run();
?>