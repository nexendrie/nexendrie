<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nexendrie\Orm\Group as GroupEntity;
use Nexendrie\Orm\GroupDummy;

require __DIR__ . "/../../bootstrap.php";

final class GroupTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Group */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Group::class);
  }
  
  public function testListOfGroups() {
    $result = $this->model->listOfGroups();
    Assert::type("array", $result);
    Assert::type(GroupDummy::class, $result[1]);
  }
  
  public function testGet() {
    $group = $this->model->get(1);
    Assert::type(GroupDummy::class, $group);
    Assert::null($this->model->get(50));
  }
  
  public function testOrmGet() {
    $group = $this->model->ormGet(1);
    Assert::type(GroupEntity::class, $group);
    Assert::null($this->model->ormGet(50));
  }
  
  public function testExists() {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
  
  public function testEdit() {
    $this->model->user = $this->getService(\Nette\Security\User::class);
    Assert::exception(function() {
      $this->model->edit(1, []);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->edit(1, []);
    }, MissingPermissionsException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->edit(5000, []);
    }, GroupNotFoundException::class);
    $group = $this->model->ormGet(1);
    $level = $group->level;
    $this->model->edit(1, ["level" => 1]);
    Assert::same(1, $group->level);
    $this->model->edit(1, ["level" => $level]);
  }
}

$test = new GroupTest();
$test->run();
?>