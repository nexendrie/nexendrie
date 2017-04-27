<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Guild as GuildEntity,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\UserJob;

require __DIR__ . "/../../bootstrap.php";

class GuildTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Guild */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Guild::class);
  }
  
  function testGetFoundingPrice() {
    Assert::type("int", $this->model->foundingPrice);
  }
  
  function testListOfGuilds() {
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
  
  function testGetGuild() {
    $guild = $this->model->getGuild(1);
    Assert::type(GuildEntity::class, $guild);
    Assert::exception(function() {
      $this->model->getGuild(50);
    }, GuildNotFoundException::class);
  }
  
  function testEditGuild() {
    Assert::exception(function() {
      $this->model->editGuild(50, []);
    }, GuildNotFoundException::class);
  }
  
  function testGetUserGuild() {
    $guild = $this->model->getUserGuild(3);
    Assert::type(GuildEntity::class, $guild);
    Assert::null($this->model->getUserGuild(1));
  }
  
  function testCanFound() {
    Assert::false($this->model->canFound());
    $this->login();
    Assert::false($this->model->canFound());
    $this->login("jakub");
    Assert::false($this->model->canFound());
    $this->login("kazimira");
    Assert::true($this->model->canFound());
  }
  
  function testFound() {
    Assert::exception(function() {
      $this->model->found([]);
    }, CannotFoundGuildException::class);
  }
  
  function testCalculateGuildIncomeBonus() {
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $job = new UserJob;
    $job->started = time();
    $orm->userJobs->attach($job);
    $job->job = 1;
    Assert::exception(function() use($job) {
      $this->model->calculateGuildIncomeBonus(100, $job);
    }, AuthenticationNeededException::class);
    $this->login();
    $job->user = 1;
    Assert::same(0, $this->model->calculateGuildIncomeBonus(100, $job));
    $this->login("jakub");
    $job->user = 3;
    $result1 = $this->model->calculateGuildIncomeBonus(100, $job);
    Assert::type("int", $result1);
    Assert::same(0, $result1);
    $job->job = 7;
    $result2 = $this->model->calculateGuildIncomeBonus(100, $job);
    Assert::type("int", $result2);
    Assert::true($result2 > 0);
    $orm->userJobs->detach($job);
  }
  
  function testCanJoin() {
    Assert::false($this->model->canJoin());
    $this->login();
    Assert::false($this->model->canJoin());
    $this->login("jakub");
    Assert::false($this->model->canJoin());
  }
  
  function testJoin() {
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
  
  function testCanLeave() {
    Assert::exception(function() {
      $this->model->canLeave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canLeave());
    $this->login("jakub");
    Assert::false($this->model->canLeave());
  }
  
  function testLeave() {
    Assert::exception(function() {
      $this->model->leave();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->leave();
    }, CannotLeaveGuildException::class);
  }
  
  function testCanManage() {
    Assert::exception(function() {
      $this->model->canManage();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canManage());
    $this->login("jakub");
    Assert::true($this->model->canManage());
  }
  
  function testCanUpgrade() {
    Assert::exception(function() {
      $this->model->canUpgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::false($this->model->canUpgrade());
    $this->login("jakub");
    Assert::type("bool", $this->model->canUpgrade());
  }
  
  function testUpgrade() {
    Assert::exception(function() {
      $this->model->upgrade();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->upgrade();
    }, CannotUpgradeGuildException::class);
  }
  
  function testGetMembers() {
    $result = $this->model->getMembers(1);
    Assert::type(ICollection::class, $result);
    Assert::count(1, $result);
    Assert::type(UserEntity::class, $result->fetch());
  }
  
  function testGetMaxRank() {
    $rank = $this->model->maxRank;
    Assert::type("int", $rank);
    Assert::same(4, $rank);
  }
  
  function testPromote() {
    Assert::exception(function() {
      $this->model->promote(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->promote(1);
    }, MissingPermissionsException::class);
    $this->login("jakub");
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
  
  function testDemote() {
    Assert::exception(function() {
      $this->model->demote(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->demote(1);
    }, MissingPermissionsException::class);
    $this->login("jakub");
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
  
  function testKick() {
    Assert::exception(function() {
      $this->model->kick(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->kick(1);
    }, MissingPermissionsException::class);
    $this->login("jakub");
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

$test = new GuildTest;
$test->run();
?>