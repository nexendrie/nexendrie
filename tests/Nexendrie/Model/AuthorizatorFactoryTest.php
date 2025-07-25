<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class AuthorizatorFactoryTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  protected \Nette\Security\Permission $model;

  protected function setUp(): void {
    $this->model = $this->getService(\Nette\Security\Permission::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testRoles(): void {
    $roles = [
      "vězeň", "cizinec", "sedlák", "bratr", "akolyta", "měšťan", "konšel", "rychtář", "kněz",
      "rytíř", "velekněz", "lord", "markrabě", "kníže", "král"
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
  
  public function testGuildRanks(): void {
    $prefix = AuthorizatorFactory::GUILD_RANK_ROLE_PREFIX;
    $ranks = [
      1 => "učedník", "tovaryš", "mistr", "cechmistr",
    ];
    foreach($ranks as $id => $rank) {
      $rank = "$prefix^$rank";
      Assert::true($this->model->hasRole($rank));
      $parents = $this->model->getRoleParents($rank);
      if($id === 1) {
        Assert::count(0, $parents);
      } else {
        Assert::count(1, $parents);
        Assert::true($this->model->roleInheritsFrom($rank, "$prefix^" . $ranks[$id - 1]));
      }
      if($id === 4) {
        foreach(AuthorizatorFactory::ORGANIZATION_PRIVILEGES as $privilege) {
          Assert::true($this->model->isAllowed($rank, AuthorizatorFactory::GUILD_RESOURCE_NAME, $privilege));
        }
      } else {
        foreach(AuthorizatorFactory::ORGANIZATION_PRIVILEGES as $privilege) {
          Assert::false($this->model->isAllowed($rank, AuthorizatorFactory::GUILD_RESOURCE_NAME, $privilege));
        }
      }
    }
  }
  
  public function testOrderRanks(): void {
    $prefix = AuthorizatorFactory::ORDER_RANK_ROLE_PREFIX;
    $ranks = [
      1 => "zbrojnoš", "rytíř", "mistr", "velmistr",
    ];
    foreach($ranks as $id => $rank) {
      $rank = "$prefix^$rank";
      Assert::true($this->model->hasRole($rank));
      $parents = $this->model->getRoleParents($rank);
      if($id === 1) {
        Assert::count(0, $parents);
      } else {
        Assert::count(1, $parents);
        Assert::true($this->model->roleInheritsFrom($rank, "$prefix^" . $ranks[$id - 1]));
      }
    }
  }
  
  public function testResources(): void {
    $resources = ["site", "poll", "article", "comment", "group", "user", "content", "event", "town"];
    foreach($resources as $resource) {
      Assert::true($this->model->hasResource($resource));
    }
  }
}

$test = new AuthorizatorFactoryTest();
$test->run();
?>