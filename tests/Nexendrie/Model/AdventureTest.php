<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Orm\Adventure as AdventureEntity;
use Nexendrie\Orm\AdventureNpc;
use Nexendrie\Orm\Mount as MountEntity;

require __DIR__ . "/../../bootstrap.php";

final class AdventureTest extends \Tester\TestCase {
  use TUserControl;

  protected Adventure $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Adventure::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testListOfAdventures(): void {
    $result = $this->model->listOfAdventures();
    Assert::type(ICollection::class, $result);
    Assert::type(AdventureEntity::class, $result->fetch());
  }
  
  public function testListOfNpcs(): void {
    $result = $this->model->listOfNpcs(1);
    Assert::type(OneHasMany::class, $result);
    $npcs = $result->getIterator();
    Assert::count(1, $npcs);
    Assert::type(AdventureNpc::class, $result->getIterator()->fetch());
    Assert::count(1, $result);
    Assert::exception(function() {
      $this->model->listOfNpcs(50);
    }, AdventureNotFoundException::class);
  }
  
  public function testGet(): void {
    $adventure = $this->model->get(1);
    Assert::type(AdventureEntity::class, $adventure);
    Assert::exception(function() {
      $this->model->get(50);
    }, AdventureNotFoundException::class);
  }
  
  public function testEditAdventure(): void {
    Assert::exception(function() {
      $this->model->editAdventure(50, []);
    }, AdventureNotFoundException::class);
    $adventure = $this->model->get(1);
    $name = $adventure->name;
    $this->model->editAdventure($adventure->id, ["name" => "abc"]);
    Assert::same("abc", $adventure->name);
    $this->model->editAdventure($adventure->id, ["name" => $name]);
  }
  
  public function testGetNpc(): void {
    $npc = $this->model->getNpc(1);
    Assert::type(AdventureNpc::class, $npc);
    Assert::exception(function() {
      $this->model->getNpc(50);
    }, AdventureNpcNotFoundException::class);
  }
  
  public function testEditNpc(): void {
    Assert::exception(function() {
      $this->model->editNpc(50, []);
    }, AdventureNpcNotFoundException::class);
    $npc = $this->model->getNpc(1);
    $name = $npc->name;
    $this->model->editNpc(1, ["name" => "abc"]);
    Assert::same("abc", $npc->name);
    $this->model->editNpc(1, ["name" => $name]);
  }
  
  public function testDeleteNpc(): void {
    Assert::exception(function() {
      $this->model->deleteNpc(50);
    }, AdventureNpcNotFoundException::class);
  }
  
  public function testFindAvailableAdventures(): void {
    Assert::exception(function() {
      $this->model->findAvailableAdventures();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findAvailableAdventures();
    Assert::type(ICollection::class, $result);
    Assert::type(AdventureEntity::class, $result->fetch());
  }
  
  public function testFindGoodMounts(): void {
    Assert::exception(function() {
      $this->model->findGoodMounts();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findGoodMounts();
    Assert::type(ICollection::class, $result);
    foreach($result as $mount) {
      Assert::type(MountEntity::class, $mount);
      Assert::true($mount->hp >= 30);
    }
  }
  
  public function testStartAdventure(): void {
    Assert::exception(function() {
      $this->model->startAdventure(50, 50);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->startAdventure(50, 50);
    }, AdventureNotFoundException::class);
    Assert::exception(function() {
      $this->model->startAdventure(1, 50);
    }, InsufficientLevelForAdventureException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->startAdventure(1, 50);
    }, MountNotFoundException::class);
    Assert::exception(function() {
      $this->model->startAdventure(1, 1);
    }, MountNotOwnedException::class);
  }
  
  public function testCalculateMonthAdventuresIncome(): void {
    Assert::type("int", $this->model->calculateMonthAdventuresIncome(1));
  }
  
  public function testCanDoAdventure(): void {
    Assert::exception(function() {
      $this->model->canDoAdventure();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->canDoAdventure());
  }
}

$test = new AdventureTest();
$test->run();
?>