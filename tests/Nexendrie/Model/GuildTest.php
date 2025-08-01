<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Guild as GuildEntity;
use Nexendrie\Orm\User as UserEntity;

require __DIR__ . "/../../bootstrap.php";

final class GuildTest extends \Tester\TestCase {
  use TUserControl;

  protected Guild $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Guild::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testListOfGuilds(): void {
    $result1 = $this->model->listOfGuilds();
    Assert::type(ICollection::class, $result1);
    Assert::count(1, $result1);
    Assert::type(GuildEntity::class, $result1->fetch());
    $result2 = $this->model->listOfGuilds(2);
    Assert::type(ICollection::class, $result2);
    Assert::count(1, $result2);
    Assert::type(GuildEntity::class, $result2->fetch());
    $result3 = $this->model->listOfGuilds(1);
    Assert::type(ICollection::class, $result3);
    Assert::count(0, $result3);
  }
  
  public function testGetGuild(): void {
    $guild = $this->model->getGuild(1);
    Assert::type(GuildEntity::class, $guild);
    Assert::exception(function() {
      $this->model->getGuild(50);
    }, GuildNotFoundException::class);
  }
  
  public function testEditGuild(): void {
    Assert::exception(function() {
      $this->model->editGuild(50, []);
    }, GuildNotFoundException::class);
    $guild = $this->model->getGuild(1);
    $name = $guild->name;
    $this->model->editGuild(1, ["name" => "abc"]);
    Assert::same("abc", $guild->name);
    $this->model->editGuild(1, ["name" => $name]);
  }
  
  public function testGetUserGuild(): void {
    $guild = $this->model->getUserGuild(3);
    Assert::type(GuildEntity::class, $guild);
    Assert::null($this->model->getUserGuild(1));
    Assert::null($this->model->getUserGuild(50));
  }
  
  public function testCanFound(): void {
    Assert::false($this->model->canFound());
    $this->login();
    Assert::false($this->model->canFound());
    $this->login("Jakub");
    Assert::false($this->model->canFound());
    $this->login("kazimira");
    Assert::true($this->model->canFound());
  }
  
  public function testFound(): void {
    Assert::exception(function() {
      $this->model->found([]);
    }, CannotFoundGuildException::class);
  }
  
  public function testCanJoin(): void {
    Assert::false($this->model->canJoin());
    $this->login();
    Assert::false($this->model->canJoin());
    $this->login("Jakub");
    Assert::false($this->model->canJoin());
  }
  
  public function testJoin(): void {
    Assert::exception(function() {
      $this->model->join(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->join(1);
    }, CannotJoinGuildException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->join(2);
    }, GuildNotFoundException::class);
  }
  
  public function testCanLeave(): void {
    Assert::exception(function() {
      $this->model->canLeave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canLeave());
    $this->login("Jakub");
    Assert::false($this->model->canLeave());
  }
  
  public function testLeave(): void {
    Assert::exception(function() {
      $this->model->leave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->leave();
    }, CannotLeaveGuildException::class);
  }
  
  public function testCanManage(): void {
    Assert::exception(function() {
      $this->model->canManage();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canManage());
    $this->login("Jakub");
    Assert::true($this->model->canManage());
  }
  
  public function testCanUpgrade(): void {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgrade());
    $this->login("Jakub");
    Assert::type("bool", $this->model->canUpgrade());
  }
  
  public function testUpgrade(): void {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgrade();
    }, CannotUpgradeGuildException::class);
  }
  
  public function testGetMembers(): void {
    $result = $this->model->getMembers(1);
    Assert::type(ICollection::class, $result);
    Assert::count(1, $result);
    Assert::type(UserEntity::class, $result->fetch());
  }
  
  public function testGetMaxRank(): void {
    $rank = $this->model->maxRank;
    Assert::type("int", $rank);
    Assert::same(4, $rank);
  }
  
  public function testPromote(): void {
    Assert::exception(function() {
      $this->model->promote(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->promote(1);
    }, MissingPermissionsException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->promote(50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->promote(1);
    }, UserNotInYourGuildException::class);
    Assert::exception(function() {
      $this->model->promote(3);
    }, CannotPromoteMemberException::class);
  }
  
  public function testDemote(): void {
    Assert::exception(function() {
      $this->model->demote(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->demote(1);
    }, MissingPermissionsException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->demote(50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->demote(1);
    }, UserNotInYourGuildException::class);
    Assert::exception(function() {
      $this->model->demote(3);
    }, CannotDemoteMemberException::class);
  }
  
  public function testKick(): void {
    Assert::exception(function() {
      $this->model->kick(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->kick(1);
    }, MissingPermissionsException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->kick(50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->kick(1);
    }, UserNotInYourGuildException::class);
    Assert::exception(function() {
      $this->model->kick(3);
    }, CannotKickMemberException::class);
  }
}

$test = new GuildTest();
$test->run();
?>