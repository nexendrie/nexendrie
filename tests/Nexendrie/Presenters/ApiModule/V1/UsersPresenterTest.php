<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class UsersPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkUsers(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["users"]);
        Assert::count($count, $json["users"]);
        foreach ($json["users"] as $user) {
            Assert::type(\stdClass::class, $user);
            Assert::type(\stdClass::class, $user->group);
            Assert::type(\stdClass::class, $user->town);
            Assert::type("array", $user->ownedTowns);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkUsers($response, 8);
        $response = $this->checkJson($action, ["associations" => ["towns" => 1]]);
        $this->checkUsers($response, 3);
        $response = $this->checkJson($action, ["associations" => ["monasteries" => 2]]);
        $this->checkUsers($response, 2);
        $response = $this->checkJson($action, ["associations" => ["guilds" => 1]]);
        $this->checkUsers($response, 1);
        $response = $this->checkJson($action, ["associations" => ["orders" => 1]]);
        $this->checkUsers($response, 2);
        $response = $this->checkJson($action, ["associations" => ["groups" => 1]]);
        $this->checkUsers($response, 1);
        $expected = ["message" => "Town with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["towns" => 50]]);
        $expected = ["message" => "Monastery with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["monasteries" => 50]]);
        $expected = ["message" => "Guild with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["guilds" => 50]]);
        $expected = ["message" => "Order with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["orders" => 50]]);
        $expected = ["message" => "Group with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["groups" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["user"]);
        Assert::type(\stdClass::class, $json["user"]->group);
        Assert::type(\stdClass::class, $json["user"]->town);
        Assert::type("array", $json["user"]->ownedTowns);
        $expected = ["message" => "User with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new UsersPresenterTest();
$test->run();
