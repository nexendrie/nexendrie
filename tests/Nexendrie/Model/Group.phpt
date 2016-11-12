<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nexendrie\Orm\Group as GroupEntity,
    Nexendrie\Orm\GroupDummy;

require __DIR__ . "/../../bootstrap.php";

class GroupTest extends \Tester\TestCase {
  use \TUserControl;
  
  /** @var Group */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Group::class);
  }
  
  function testListOfGroups() {
    $result = $this->model->listOfGroups();
    Assert::type("array", $result);
    Assert::type(GroupDummy::class, $result[1]);
  }
  
  function testNumberOfMembers() {
    $result1 = $this->model->numberOfMembers(1);
    Assert::type("int", $result1);
    Assert::same(1, $result1);
    $result2 = $this->model->numberOfMembers(50);
    Assert::type("int", $result2);
    Assert::same(0, $result2);
  }
  
  function testGet() {
    $group = $this->model->get(1);
    Assert::type(GroupDummy::class, $group);
    Assert::false($this->model->get(50));
  }
  
  function testGetByLevel() {
    $group = $this->model->getByLevel(10000);
    Assert::type(GroupDummy::class, $group);
    Assert::same(1, $group->id);
    Assert::false($this->model->getByLevel(100000));
  }
  
  function testOrmGet() {
    $group = $this->model->ormGet(1);
    Assert::type(GroupEntity::class, $group);
    Assert::false($this->model->ormGet(50));
  }
  
  function testExists() {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
  
  function testEdit() {
    $this->model->user = $this->getService(\Nette\Security\User::class);
    Assert::exception(function() {
      $this->model->edit(1, []);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->edit(1, []);
    }, MissingPermissionsException::class);
  }
}

$test = new GroupTest;
$test->run();
?>