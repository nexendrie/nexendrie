<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

final class TownsOwnedAchievementsTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var ProducedBeersAchievement */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  protected function setUp() {
    $this->model = $this->getService(TownsOwnedAchievements::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  public function testGetName() {
    Assert::same("Vládce", $this->model->getName());
  }
  
  public function testIsAchievedAndGetProgress() {
    $user = $this->orm->users->getById(1);
    Assert::same(2, $this->model->getProgress($user));
    Assert::same(1, $this->model->isAchieved($user));
    $user = $this->orm->users->getById(3);
    Assert::same(0, $this->model->getProgress($user));
    Assert::same(0, $this->model->isAchieved($user));
  }
}

$test = new TownsOwnedAchievementsTest();
$test->run();
?>