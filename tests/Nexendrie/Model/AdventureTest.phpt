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
  
  /** @var Adventure */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Adventure::class);
  }
  
  public function testListOfAdventures() {
    $result = $this->model->listOfAdventures();
    Assert::type(ICollection::class, $result);
    Assert::type(AdventureEntity::class, $result->fetch());
  }
  
  public function testListOfNpcs() {
    $result = $this->model->listOfNpcs(1);
    Assert::type(OneHasMany::class, $result);
    Assert::type(AdventureNpc::class, $result->get()->fetch());
    Assert::count(1, $result);
    Assert::exception(function() {
      $this->model->listOfNpcs(50);
    }, AdventureNotFoundException::class);
  }
  
  public function testGet() {
    $adventure = $this->model->get(1);
    Assert::type(AdventureEntity::class, $adventure);
    Assert::exception(function() {
      $this->model->get(50);
    }, AdventureNotFoundException::class);
  }
  
  public function testEditAdventure() {
    Assert::exception(function() {
      $this->model->editAdventure(50, []);
    }, AdventureNotFoundException::class);
    $adventure = $this->model->get(1);
    $name = $adventure->name;
    $this->model->editAdventure($adventure->id, ["name" => "abc"]);
    Assert::same("abc", $adventure->name);
    $this->model->editAdventure($adventure->id, ["name" => $name]);
  }
  
  public function testGetNpc() {
    $npc = $this->model->getNpc(1);
    Assert::type(AdventureNpc::class, $npc);
    Assert::exception(function() {
      $this->model->getNpc(50);
    }, AdventureNpcNotFoundException::class);
  }
  
  public function testEditNpc() {
    Assert::exception(function() {
      $this->model->editNpc(50, []);
    }, AdventureNpcNotFoundException::class);
    $npc = $this->model->getNpc(1);
    $name = $npc->name;
    $this->model->editNpc(1, ["name" => "abc"]);
    Assert::same("abc", $npc->name);
    $this->model->editNpc(1, ["name" => $name]);
  }
  
  public function testDeleteNpc() {
    Assert::exception(function() {
      $this->model->deleteNpc(50);
    }, AdventureNpcNotFoundException::class);
  }
  
  public function testFindAvailableAdventures() {
    Assert::exception(function() {
      $this->model->findAvailableAdventures();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findAvailableAdventures();
    Assert::type(ICollection::class, $result);
    Assert::type(AdventureEntity::class, $result->fetch());
  }
  
  public function testFindGoodMounts() {
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
  
  public function testStartAdventure() {
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
  
  public function testCalculateMonthAdventuresIncome() {
    Assert::type("int", $this->model->calculateMonthAdventuresIncome(1));
  }
  
  public function testCanDoAdventure() {
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