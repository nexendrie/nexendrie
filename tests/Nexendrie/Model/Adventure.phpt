<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nextras\Orm\Relationships\OneHasMany,
    Nexendrie\Orm\Adventure as AdventureEntity,
    Nexendrie\Orm\AdventureNpc,
    Nexendrie\Orm\Mount as MountEntity;

require __DIR__ . "/../../bootstrap.php";

class AdventureTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Adventure */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Adventure::class);
  }
  
  function testListOfAdventures() {
    $result = $this->model->listOfAdventures();
    Assert::type(ICollection::class, $result);
    Assert::type(AdventureEntity::class, $result->fetch());
  }
  
  function testListOfNpcs() {
    $result = $this->model->listOfNpcs(1);
    Assert::type(OneHasMany::class, $result);
    Assert::type(AdventureNpc::class, $result->get()->fetch());
    Assert::count(1, $result);
    Assert::exception(function() {
      $this->model->listOfNpcs(50);
    }, AdventureNotFoundException::class);
  }
  
  function testGet() {
    $adventure = $this->model->get(1);
    Assert::type(AdventureEntity::class, $adventure);
    Assert::exception(function() {
      $this->model->get(50);
    }, AdventureNotFoundException::class);
  }
  
  function testEditAdventure() {
    Assert::exception(function() {
      $this->model->editAdventure(50, []);
    }, AdventureNotFoundException::class);
    $adventure = $this->model->get(1);
    $name = $adventure->name;
    $this->model->editAdventure($adventure->id, ["name" => "abc"]);
    Assert::same("abc", $adventure->name);
    $this->model->editAdventure($adventure->id, ["name" => $name]);
  }
  
  function testGetNpc() {
    $npc = $this->model->getNpc(1);
    Assert::type(AdventureNpc::class, $npc);
    Assert::exception(function() {
      $this->model->getNpc(50);
    }, AdventureNpcNotFoundException::class);
  }
  
  function testEditNpc() {
    Assert::exception(function() {
      $this->model->editNpc(50, []);
    }, AdventureNpcNotFoundException::class);
  }
  
  function testDeleteNpc() {
    Assert::exception(function() {
      $this->model->deleteNpc(50);
    }, AdventureNpcNotFoundException::class);
  }
  
  function testFindAvailableAdventures() {
    Assert::exception(function() {
      $this->model->findAvailableAdventures();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findAvailableAdventures();
    Assert::type(ICollection::class, $result);
    Assert::type(AdventureEntity::class, $result->fetch());
  }
  
  function testFindGoodMounts() {
    Assert::exception(function() {
      $this->model->findGoodMounts();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findGoodMounts();
    Assert::type(ICollection::class, $result);
    /** @var MountEntity $mount */
    $mount = $result->fetch();
    Assert::type(MountEntity::class, $mount);
    Assert::true($mount->hp >= 30);
  }
  
  function testGetNextNpc() {
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $adventure = new \Nexendrie\Orm\UserAdventure;
    $orm->userAdventures->attach($adventure);
    $adventure->started = time();
    $adventure->user = 1;
    $adventure->adventure = 1;
    $adventure->mount = 2;
    $npc = $this->model->getNextNpc($adventure);
    Assert::type(AdventureNpc::class, $npc);
    $adventure->progress = 2;
    $npc = $this->model->getNextNpc($adventure);
    Assert::null($npc);
    $adventure->progress = 10;
    $npc = $this->model->getNextNpc($adventure);
    Assert::null($npc);
    $orm->userAdventures->detach($adventure);
  }
  
  function testCalculateMonthAdventuresIncome() {
    Assert::type("int", $this->model->calculateMonthAdventuresIncome(1));
  }
  
  function testCanDoAdventure() {
    Assert::exception(function() {
      $this->model->canDoAdventure();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->canDoAdventure());
  }
}

$test = new AdventureTest;
$test->run();
?>