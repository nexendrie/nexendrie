<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Skill;

require __DIR__ . "/../../bootstrap.php";

class SkillsTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Skills */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Skills::class);
  }
  
  function testListOfSkills() {
    $result1 = $this->model->listOfSkills();
    Assert::type(ICollection::class, $result1);
    Assert::count(9, $result1);
    $result2 = $this->model->listOfSkills(Skill::TYPE_COMBAT);
    Assert::type(ICollection::class, $result2);
    Assert::count(3, $result2);
    /** @var Skill $skill */
    $skill = $result2->fetch();
    Assert::type(Skill::class, $skill);
    Assert::same(Skill::TYPE_COMBAT, $skill->type);
  }
  
  function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, SkillNotFoundException::class);
  }
  
  function testGet() {
    $skill = $this->model->get(1);
    Assert::type(Skill::class, $skill);
    Assert::exception(function() {
      $this->model->get(50);
    }, SkillNotFoundException::class);
  }
  
  function testCalculateLearningPrice() {
    Assert::same(100, $this->model->calculateLearningPrice(100, 1));
    $result = $this->model->calculateLearningPrice(100, 5);
    Assert::type("int", $result);
    Assert::true($result > 100);
  }
  
  function testLearn() {
    Assert::exception(function() {
      $this->model->learn(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->learn(50);
    }, SkillNotFoundException::class);
    Assert::exception(function() {
      $this->model->learn(3);
    }, SkillMaxLevelReachedException::class);
  }
  
  function testGetLevelOfSkill() {
    Assert::exception(function() {
      $this->model->getLevelOfSkill(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::same(0, $this->model->getLevelOfSkill(1));
    $level = $this->model->getLevelOfSkill(3);
    Assert::type("int", $level);
    Assert::same(5, $level);
  }
  
  function testCalculateSkillSuccessBonus() {
    Assert::exception(function() {
      $this->model->calculateSkillSuccessBonus(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::same(0, $this->model->calculateSkillSuccessBonus(1));
    $result = $this->model->calculateSkillSuccessBonus(3);
    Assert::type("int", $result);
    Assert::true($result > 0);
  }
}

$test = new SkillsTest;
$test->run();
?>