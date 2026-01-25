<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

final class AdventuresPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkAdventures(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["adventures"]);
        Assert::count($count, $json["adventures"]);
        foreach ($json["adventures"] as $adventure) {
            Assert::type(\stdClass::class, $adventure);
            Assert::type("array", $adventure->npcs);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkAdventures($response, 4);
        $response = $this->checkJson($action, ["associations" => ["events" => 1]]);
        $this->checkAdventures($response, 0);
        $expected = ["message" => "Event with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["events" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["adventure"]);
        Assert::type("array", $json["adventure"]->npcs);
        $expected = ["message" => "Adventure with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new AdventuresPresenterTest();
$test->run();
