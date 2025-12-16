<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class NpcsPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkNpcs(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["npcs"]);
        Assert::count($count, $json["npcs"]);
        foreach ($json["npcs"] as $npc) {
            Assert::type(\stdClass::class, $npc);
            Assert::type(\stdClass::class, $npc->adventure);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $expected = ["message" => "This action is not allowed."];
        $this->checkJsonScheme($action, $expected);
        $response = $this->checkJson($action, ["associations" => ["adventures" => 1]]);
        $this->checkNpcs($response, 1);
        $expected = ["message" => "Adventure with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["adventures" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $expected = ["message" => "This action is not allowed."];
        $this->checkJsonScheme($action, $expected, ["id" => 1]);
        $response = $this->checkJson($action, ["id" => 1, "associations" => ["adventures" => 1]]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["npc"]);
        Assert::type(\stdClass::class, $json["npc"]->adventure);
        $expected = ["message" => "Npc with id 1 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 1, "associations" => ["adventures" => 2]]);
    }
}

$test = new NpcsPresenterTest();
$test->run();
