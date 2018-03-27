<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class CombatHelperTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var CombatHelper */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  protected function setUp() {
    $this->model = $this->getService(CombatHelper::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  public function testCalculateUserLife() {
    $result = $this->model->calculateUserLife($this->orm->users->getById(1));
    Assert::type("array", $result);
    Assert::count(2, $result);
    Assert::type("int", $result["maxLife"]);
    Assert::type("int", $result["life"]);
  }
  
  public function testCalculateUserDamage() {
    $damage1 = $this->model->calculateUserDamage($this->orm->users->getById(1));
    Assert::type("int", $damage1);
    $damage2 = $this->model->calculateUserDamage($this->orm->users->getById(1), $this->orm->mounts->getById(2));
    Assert::type("int", $damage2);
    Assert::true($damage2 === $damage1 + 7);
  }
  
  public function testCalculateUserArmor() {
    $armor1 = $this->model->calculateUserArmor($this->orm->users->getById(1));
    Assert::type("int", $armor1);
    $armor2 = $this->model->calculateUserArmor($this->orm->users->getById(1), $this->orm->mounts->getById(2));
    Assert::type("int", $armor2);
    Assert::true($armor2 === $armor1 + 5);
  }
  
  public function testUserCombatStats() {
    $result = $this->model->userCombatStats($this->orm->users->getById(1));
    Assert::type("array", $result);
    Assert::count(4, $result);
    foreach($result as $value) {
      Assert::type("int", $value);
    }
  }
}

$test = new CombatHelperTest();
$test->run();
?>