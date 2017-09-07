<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class AuthorizatorFactoryTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var \Nette\Security\Permission */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(\Nette\Security\Permission::class);
  }
  
  public function testRoles() {
    $roles = [
      "vězeň", "cizinec", "sedlák", "bratr", "akolyta", "měšťan", "konšel", "rychtář", "kněz",
      "rytíř", "vyšší kněz", "lord", "vévoda", "jeho Jasnost", "jeho Veličenstvo"
    ];
    foreach($roles as $role) {
      Assert::true($this->model->hasRole($role));
      $parents = $this->model->getRoleParents($role);
      if($role === "vězeň" OR $role === "cizinec") {
        Assert::count(0, $parents);
      } elseif($role === "sedlák") {
        Assert::true(count($parents) > 0);
        Assert::true($this->model->roleInheritsFrom($role, "cizinec"));
      } else {
        Assert::true(count($parents) > 0);
        Assert::true($this->model->roleInheritsFrom($role, "sedlák"));
      }
    }
  }
  
  public function testResources() {
    $resources = ["site", "poll", "article", "comment", "group", "user", "content", "event", "town"];
    foreach($resources as $resource) {
      Assert::true($this->model->hasResource($resource));
    }
  }
}

$test = new AuthorizatorFactoryTest;
$test->run();
?>