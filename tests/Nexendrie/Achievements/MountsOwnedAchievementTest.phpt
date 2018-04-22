<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

final class MountsOwnedAchievementTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var MountsOwnedAchievement */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  protected function setUp() {
    $this->model = $this->getService(MountsOwnedAchievement::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  public function testGetName() {
    Assert::same("Chovatel", $this->model->getName());
  }
  
  public function testIsAchievedAndGetProgress() {
    $user = $this->orm->users->getById(0);
    Assert::same(7, $this->model->getProgress($user));
    Assert::same(2, $this->model->isAchieved($user));
    $user = $this->orm->users->getById(7);
    Assert::same(0, $this->model->getProgress($user));
    Assert::same(0, $this->model->isAchieved($user));
  }
}

$test = new MountsOwnedAchievementTest();
$test->run();
?>