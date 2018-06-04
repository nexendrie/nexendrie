<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;
use Nexendrie\Orm\UserSkill;
use Nexendrie\Orm\User;

final class LessonsTakenAchievementTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var LessonsTakenAchievement */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  protected function setUp() {
    $this->model = $this->getService(LessonsTakenAchievement::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  protected function generateSkill(User $user): UserSkill {
    $skill = new UserSkill();
    $this->orm->userSkills->attach($skill);
    $skill->user = $user;
    $skill->skill = 1;
    $skill->level = 1;
    return $skill;
  }
  
  public function testGetName() {
    Assert::same("Student", $this->model->getName());
  }
  
  public function testIsAchievedAndGetProgress() {
    $user = $this->orm->users->getById(7);
    Assert::same(0, $this->model->getProgress($user));
    Assert::same(0, $this->model->isAchieved($user));
    $skills = [];
    $skills[] = $this->generateSkill($user);
    $this->orm->userSkills->persistAndFlush($skills[0]);
    Assert::same(1, $this->model->getProgress($user));
    Assert::same(1, $this->model->isAchieved($user));
    $finalCount = $this->model->getRequirements()[3];
    for($i = 2; $i <= $finalCount; $i++) {
      $skills[] = $skill = $this->generateSkill($user);
      $this->orm->userSkills->persist($skill);
    }
    $this->orm->userSkills->flush();
    Assert::same($finalCount, $this->model->getProgress($user));
    Assert::same(4, $this->model->isAchieved($user));
    foreach($skills as $skill) {
      $this->orm->userSkills->remove($skill);
    }
    $this->orm->userSkills->flush();
  }
}

$test = new LessonsTakenAchievementTest();
$test->run();
?>