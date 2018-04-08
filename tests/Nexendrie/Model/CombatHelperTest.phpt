<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    HeroesofAbenez\Combat\Character;

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
  
  public function testGetCharacter() {
    Assert::exception(function() {
      $this->model->getCharacter(5000);
    }, UserNotFoundException::class);
    $character = $this->model->getCharacter(1);
    Assert::type(Character::class, $character);
    Assert::count(3, $character->equipment);
    Assert::same(110, $character->maxHitpoints);
    $character->calculateInitiative();
    Assert::same(1, $character->initiative);
  }
  
  public function testGetAdventureNpc() {
    $npc = $this->orm->adventureNpcs->getById(1);
    $character = $this->model->getAdventureNpc($npc);
    Assert::type(Character::class, $character);
    Assert::same(20, $character->maxHitpoints);
    Assert::count(1, $character->equipment);
    Assert::same($npc->strength, $character->damage);
    $character->calculateInitiative();
    Assert::same(0, $character->initiative);
  }
}

$test = new CombatHelperTest();
$test->run();
?>