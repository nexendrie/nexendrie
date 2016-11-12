<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Order as OrderEntity,
    Nexendrie\Orm\User as UserEntity;

require __DIR__ . "/../../bootstrap.php";

class OrderTest extends \Tester\TestCase {
  use \TUserControl;
  
  /** @var Order */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Order::class);
  }
  
  function testGetFoundingPrice() {
    Assert::type("int", $this->model->foundingPrice);
  }
  
  function testListOfOrders() {
    $result = $this->model->listOfOrders();
    Assert::type(ICollection::class, $result);
    Assert::type(OrderEntity::class, $result->fetch());
  }
  
  function testGetOrder() {
    $order = $this->model->getOrder(1);
    Assert::type(OrderEntity::class, $order);
    Assert::exception(function() {
      $this->model->getOrder(50);
    }, OrderNotFoundException::class);
  }
  
  function testEditOrder() {
    Assert::exception(function() {
      $this->model->editOrder(50, []);
    }, OrderNotFoundException::class);
  }
  
  function testGetUserOrder() {
    $order = $this->model->getUserOrder(1);
    Assert::type(OrderEntity::class, $order);
    Assert::null($this->model->getUserOrder(2));
  }
  
  function testCanFound() {
    Assert::false($this->model->canFound());
    $this->login();
    Assert::false($this->model->canFound());
    $this->login("jakub");
    Assert::false($this->model->canFound());
    $this->login("system");
    Assert::true($this->model->canFound());
    $this->login("svetlana");
    Assert::false($this->model->canFound());
  }
  
  function testFound() {
    $order = $this->model->getOrder(1);
    $data = ["name" => $order->name];
    Assert::exception(function() use($data) {
      $this->model->found($data);
    }, CannotFoundOrderException::class);
  }
  
  function testCalculateOrderIncomeBonus() {
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
  
  function testCanJoin() {
    Assert::false($this->model->canJoin());
    $this->login();
    Assert::false($this->model->canJoin());
    $this->login("jakub");
    Assert::false($this->model->canJoin());
    $this->login("system");
    Assert::true($this->model->canJoin());
    $this->login("svetlana");
    Assert::false($this->model->canJoin());
  }
  
  function testJoin() {
    Assert::exception(function() {
      $this->model->join(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->join(1);
    }, CannotJoinOrderException::class);
  }
  
  function testCanLeave() {
    Assert::exception(function() {
      $this->model->canLeave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canLeave());
    $this->login("jakub");
    Assert::false($this->model->canLeave());
    $this->login("system");
    Assert::false($this->model->canLeave());
    $this->login("svetlana");
    Assert::true($this->model->canLeave());
  }
  
  function testLeave() {
    Assert::exception(function() {
      $this->model->leave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->leave();
    }, CannotLeaveOrderException::class);
  }
  
  function testCanManage() {
    Assert::exception(function() {
      $this->model->canManage();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::true($this->model->canManage());
    $this->login("jakub");
    Assert::false($this->model->canManage());
    $this->login("system");
    Assert::false($this->model->canManage());
    $this->login("svetlana");
    Assert::false($this->model->canManage());
  }
  
  function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->canUpgrade());
    $this->login("jakub");
    Assert::false($this->model->canUpgrade());
    $this->login("system");
    Assert::false($this->model->canUpgrade());
    $this->login("svetlana");
    Assert::false($this->model->canUpgrade());
  }
  
  function testUpgrade() {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login("svetlana");
    Assert::exception(function() {
      $this->model->upgrade();
    }, CannotUpgradeOrderException::class);
  }
  
  function testGetMembers() {
    $result1 = $this->model->getMembers(1);
    Assert::type(ICollection::class, $result1);
    Assert::count(2, $result1);
    Assert::type(UserEntity::class, $result1->fetch());
    $result2 = $this->model->getMembers(50);
    Assert::type(ICollection::class, $result2);
    Assert::count(0, $result2);
  }
  
  function testGetMaxRank() {
    $rank = $this->model->maxRank;
    Assert::type("int", $rank);
    Assert::same(4, $rank);
  }
  
  function testPromote() {
    Assert::exception(function() {
      $this->model->promote(1);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
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
  
  function testDemote() {
    Assert::exception(function() {
      $this->model->demote(1);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
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
  
  function testKick() {
    Assert::exception(function() {
      $this->model->kick(1);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
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

$test = new OrderTest;
$test->run();
?>