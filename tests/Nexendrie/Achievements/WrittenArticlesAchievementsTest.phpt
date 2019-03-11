<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

final class WrittenArticlesAchievementsTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var WrittenArticlesAchievement */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  protected function setUp() {
    $this->model = $this->getService(WrittenArticlesAchievement::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  public function testGetName() {
    Assert::same("Kronikář", $this->model->getName());
  }
  
  public function testIsAchievedAndGetProgress() {
    $user = $this->orm->users->getById(1);
    Assert::same(15, $this->model->getProgress($user));
    Assert::same(3, $this->model->isAchieved($user));
    $user = $this->orm->users->getById(3);
    Assert::same(0, $this->model->getProgress($user));
    Assert::same(0, $this->model->isAchieved($user));
  }
}

$test = new WrittenArticlesAchievementsTest();
$test->run();
?>