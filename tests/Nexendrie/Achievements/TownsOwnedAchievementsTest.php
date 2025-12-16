<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Nexendrie\Orm\User;
use Tester\Assert;

final class TownsOwnedAchievementsTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    protected TownsOwnedAchievements $model;
    protected \Nexendrie\Orm\Model $orm;

    protected function setUp(): void
    {
        $this->model = $this->getService(TownsOwnedAchievements::class); // @phpstan-ignore assign.propertyType
        $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
    }

    public function testGetName(): void
    {
        Assert::same("Vládce", $this->model->getName());
    }

    public function testIsAchievedAndGetProgress(): void
    {
        /** @var User $user */
        $user = $this->orm->users->getById(1);
        Assert::same(2, $this->model->getProgress($user));
        Assert::same(1, $this->model->isAchieved($user));
        /** @var User $user */
        $user = $this->orm->users->getById(3);
        Assert::same(0, $this->model->getProgress($user));
        Assert::same(0, $this->model->isAchieved($user));
    }
}

$test = new TownsOwnedAchievementsTest();
$test->run();
