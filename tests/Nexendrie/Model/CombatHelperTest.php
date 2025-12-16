<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\AdventureNpc;
use Nexendrie\Orm\User;
use Tester\Assert;
use HeroesofAbenez\Combat\Character;

require __DIR__ . "/../../bootstrap.php";

final class CombatHelperTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    protected CombatHelper $model;
    protected \Nexendrie\Orm\Model $orm;

    protected function setUp(): void
    {
        $this->model = $this->getService(CombatHelper::class); // @phpstan-ignore assign.propertyType
        $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
    }

    public function testCalculateUserLife(): void
    {
        /** @var User $user */
        $user = $this->orm->users->getById(1);
        $result = $this->model->calculateUserLife($user);
        Assert::type("array", $result);
        Assert::count(2, $result);
        Assert::type("int", $result["maxLife"]);
        Assert::type("int", $result["life"]);
    }

    public function testGetCharacter(): void
    {
        Assert::exception(function () {
            $this->model->getCharacter(5000);
        }, UserNotFoundException::class);
        /** @var Character $character */
        $character = $this->model->getCharacter(1);
        Assert::type(Character::class, $character);
        Assert::count(3, $character->equipment);
        Assert::same(110, $character->maxHitpoints);
        Assert::same(1, $character->initiative);
    }

    public function testGetAdventureNpc(): void
    {
        /** @var AdventureNpc $npc */
        $npc = $this->orm->adventureNpcs->getById(1);
        /** @var Character $character */
        $character = $this->model->getAdventureNpc($npc);
        Assert::type(Character::class, $character);
        Assert::same(20, $character->maxHitpoints);
        Assert::count(1, $character->equipment);
        Assert::same($npc->strength, $character->damage);
        Assert::same($npc->initiative, $character->initiative);
    }
}

$test = new CombatHelperTest();
$test->run();
