<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;
use Nexendrie\Orm\UserAdventure;
use Nexendrie\Orm\User;

final class CompletedAdventuresAchievementTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  protected CompletedAdventuresAchievement $model;
  protected \Nexendrie\Orm\Model $orm;
  
  protected function setUp(): void {
    $this->model = $this->getService(CompletedAdventuresAchievement::class); // @phpstan-ignore assign.propertyType
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
  }
  
  protected function generateAdventure(User $user): UserAdventure {
    $adventure = new UserAdventure();
    $this->orm->userAdventures->attach($adventure);
    $adventure->user = $user;
    $adventure->adventure = 1;
    $adventure->mount = 1;
    $adventure->created = time();
    $adventure->progress = UserAdventure::PROGRESS_COMPLETED;
    return $adventure;
  }
  
  public function testGetName(): void {
    Assert::same("Dobrodruh", $this->model->getName());
  }
  
  public function testIsAchievedAndGetProgress(): void {
    /** @var User $user */
    $user = $this->orm->users->getById(1);
    Assert::same(0, $this->model->getProgress($user));
    Assert::same(0, $this->model->isAchieved($user));
    $adventures = [];
    $adventures[] = $this->generateAdventure($user);
    $this->orm->userAdventures->persistAndFlush($adventures[0]);
    Assert::same(1, $this->model->getProgress($user));
    Assert::same(1, $this->model->isAchieved($user));
    $finalCount = $this->model->getRequirements()[3];
    for($i = 2; $i <= $finalCount; $i++) {
      $adventures[] = $adventure = $this->generateAdventure($user);
      $this->orm->userAdventures->persist($adventure);
    }
    $this->orm->userAdventures->flush();
    Assert::same($finalCount, $this->model->getProgress($user));
    Assert::same(4, $this->model->isAchieved($user));
    foreach($adventures as $adventure) {
      $this->orm->userAdventures->remove($adventure);
    }
    $this->orm->userAdventures->flush();
  }
}

$test = new CompletedAdventuresAchievementTest();
$test->run();
?>