<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

final class GroupsPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $json = $response->getPayload();
        Assert::type("array", $json["groups"]);
        Assert::count(15, $json["groups"]);
        foreach ($json["groups"] as $group) {
            Assert::type(\stdClass::class, $group);
            Assert::type("array", $group->members);
        }
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["group"]);
        Assert::type("array", $json["group"]->members);
        $expected = ["message" => "Group with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new GroupsPresenterTest();
$test->run();
