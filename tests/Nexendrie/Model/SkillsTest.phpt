<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Skill;

require __DIR__ . "/../../bootstrap.php";

final class SkillsTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Skills */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Skills::class);
  }
  
  public function testListOfSkills() {
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
  
  public function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, SkillNotFoundException::class);
    $skill = $this->model->get(1);
    $name = $skill->name;
    $this->model->edit($skill->id, ["name" => "abc"]);
    Assert::same("abc", $skill->name);
    $this->model->edit($skill->id, ["name" => $name]);
  }
  
  public function testGet() {
    $skill = $this->model->get(1);
    Assert::type(Skill::class, $skill);
    Assert::exception(function() {
      $this->model->get(50);
    }, SkillNotFoundException::class);
  }
  
  public function testLearn() {
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
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->learn(1);
      });
    }, InsufficientFundsException::class);
  }
  
  public function testGetLevelOfSkill() {
    Assert::exception(function() {
      $this->model->getLevelOfSkill(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::same(0, $this->model->getLevelOfSkill(1));
    $level = $this->model->getLevelOfSkill(3);
    Assert::type("int", $level);
    Assert::same(5, $level);
  }
  
  public function testCalculateSkillSuccessBonus() {
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

$test = new SkillsTest();
$test->run();
?>