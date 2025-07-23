<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Order as OrderEntity;
use Nexendrie\Orm\User as UserEntity;

require __DIR__ . "/../../bootstrap.php";

final class OrderTest extends \Tester\TestCase {
  use TUserControl;

  protected Order $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Order::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testListOfOrders(): void {
    $result = $this->model->listOfOrders();
    Assert::type(ICollection::class, $result);
    Assert::type(OrderEntity::class, $result->fetch());
  }
  
  public function testGetOrder(): void {
    $order = $this->model->getOrder(1);
    Assert::type(OrderEntity::class, $order);
    Assert::exception(function() {
      $this->model->getOrder(50);
    }, OrderNotFoundException::class);
  }
  
  public function testEditOrder(): void {
    Assert::exception(function() {
      $this->model->editOrder(50, []);
    }, OrderNotFoundException::class);
    $order = $this->model->getOrder(1);
    $name = $order->name;
    $this->model->editOrder(1, ["name" => "abc"]);
    Assert::same("abc", $order->name);
    $this->model->editOrder(1, ["name" => $name]);
  }
  
  public function testGetUserOrder(): void {
    $order = $this->model->getUserOrder(1);
    Assert::type(OrderEntity::class, $order);
    Assert::null($this->model->getUserOrder(2));
    Assert::null($this->model->getUserOrder(5000));
  }
  
  public function testCanFound(): void {
    Assert::false($this->model->canFound());
    $this->login();
    Assert::false($this->model->canFound());
    $this->login("Jakub");
    Assert::false($this->model->canFound());
    $this->login("Vladěna");
    Assert::true($this->model->canFound());
    $this->login("Světlana");
    Assert::false($this->model->canFound());
  }
  
  public function testFound(): void {
    $order = $this->model->getOrder(1);
    $data = ["name" => $order->name];
    Assert::exception(function() use($data) {
      $this->model->found($data);
    }, CannotFoundOrderException::class);
    $this->login("Vladěna");
    Assert::exception(function() use($data) {
      $this->model->found($data);
    }, OrderNameInUseException::class);
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->found(["name" => "abc"]);
      });
    }, InsufficientFundsException::class);
  }
  
  public function testCalculateOrderIncomeBonus(): void {
    Assert::exception(function() {
      $this->model->calculateOrderIncomeBonus(100);
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->calculateOrderIncomeBonus(100);
    Assert::type("int", $result);
    Assert::true($result > 0);
    $this->login("Rahym");
    Assert::same(0, $this->model->calculateOrderIncomeBonus(100));
  }
  
  public function testCanJoin(): void {
    Assert::false($this->model->canJoin());
    $this->login();
    Assert::false($this->model->canJoin());
    $this->login("Jakub");
    Assert::false($this->model->canJoin());
    $this->login("Vladěna");
    Assert::true($this->model->canJoin());
    $this->login("Světlana");
    Assert::false($this->model->canJoin());
  }
  
  public function testJoin(): void {
    Assert::exception(function() {
      $this->model->join(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->join(1);
    }, CannotJoinOrderException::class);
    $this->login("Vladěna");
    Assert::exception(function() {
      $this->model->join(5000);
    }, OrderNotFoundException::class);
  }
  
  public function testCanLeave(): void {
    Assert::exception(function() {
      $this->model->canLeave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canLeave());
    $this->login("Jakub");
    Assert::false($this->model->canLeave());
    $this->login("Vladěna");
    Assert::false($this->model->canLeave());
    $this->login("Světlana");
    Assert::true($this->model->canLeave());
  }
  
  public function testLeave(): void {
    Assert::exception(function() {
      $this->model->leave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->leave();
    }, CannotLeaveOrderException::class);
    $this->login("Světlana");
    $user = $this->getUser();
    $this->preserveStats(["order", "orderRank"], function() use($user) {
      $this->model->leave();
      Assert::null($user->order);
      Assert::null($user->orderRank);
    });
  }
  
  public function testCanManage(): void {
    Assert::exception(function() {
      $this->model->canManage();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::true($this->model->canManage());
    $this->login("Jakub");
    Assert::false($this->model->canManage());
    $this->login("Vladěna");
    Assert::false($this->model->canManage());
    $this->login("Světlana");
    Assert::false($this->model->canManage());
  }
  
  public function testCanUpgrade(): void {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::true($this->model->canUpgrade());
    $this->modifyOrder(["level" => OrderEntity::MAX_LEVEL], function() {
      Assert::false($this->model->canUpgrade());
    });
    $this->login("Jakub");
    Assert::false($this->model->canUpgrade());
    $this->login("Vladěna");
    Assert::false($this->model->canUpgrade());
    $this->login("Světlana");
    Assert::false($this->model->canUpgrade());
  }
  
  public function testUpgrade(): void {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login("Světlana");
    Assert::exception(function() {
      $this->model->upgrade();
    }, CannotUpgradeOrderException::class);
    $this->login();
    Assert::exception(function() {
      $this->modifyOrder(["money" => 0], function() {
        $this->model->upgrade();
      });
    }, InsufficientFundsException::class);
  }
  
  public function testGetMembers(): void {
    $result1 = $this->model->getMembers(1);
    Assert::type(ICollection::class, $result1);
    Assert::count(2, $result1);
    Assert::type(UserEntity::class, $result1->fetch());
    $result2 = $this->model->getMembers(50);
    Assert::type(ICollection::class, $result2);
    Assert::count(0, $result2);
  }
  
  public function testGetMaxRank(): void {
    $rank = $this->model->maxRank;
    Assert::type("int", $rank);
    Assert::same(4, $rank);
  }
  
  public function testPromote(): void {
    Assert::exception(function() {
      $this->model->promote(1);
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->promote(1);
    }, MissingPermissionsException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->promote(50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->promote(0);
    }, UserNotInYourOrderException::class);
    Assert::exception(function() {
      $this->model->promote(1);
    }, CannotPromoteMemberException::class);
  }
  
  public function testDemote(): void {
    Assert::exception(function() {
      $this->model->demote(1);
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->demote(1);
    }, MissingPermissionsException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->demote(50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->demote(0);
    }, UserNotInYourOrderException::class);
  }
  
  public function testKick(): void {
    Assert::exception(function() {
      $this->model->kick(1);
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->kick(1);
    }, MissingPermissionsException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->kick(50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->kick(0);
    }, UserNotInYourOrderException::class);
    Assert::exception(function() {
      $this->model->kick(1);
    }, CannotKickMemberException::class);
  }
}

$test = new OrderTest();
$test->run();
?>