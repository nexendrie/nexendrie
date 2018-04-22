<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

final class ProducedBeersAchievementTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var ProducedBeersAchievement */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  protected function setUp() {
    $this->model = $this->getService(ProducedBeersAchievement::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  public function testGetName() {
    Assert::same("Pivař", $this->model->getName());
  }
  
  public function testIsAchievedAndGetProgress() {
    $user = $this->orm->users->getById(1);
    Assert::same(0, $this->model->getProgress($user));
    Assert::same(0, $this->model->isAchieved($user));
    $user = $this->orm->users->getById(3);
    Assert::same(65, $this->model->getProgress($user));
    Assert::same(4, $this->model->isAchieved($user));
  }
}

$test = new ProducedBeersAchievementTest();
$test->run();
?>