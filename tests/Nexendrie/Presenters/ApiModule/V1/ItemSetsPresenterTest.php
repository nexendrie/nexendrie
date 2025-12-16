<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class ItemSetsPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $json = $response->getPayload();
        Assert::type("array", $json["itemSets"]);
        Assert::count(6, $json["itemSets"]);
        foreach ($json["itemSets"] as $itemSet) {
            Assert::type(\stdClass::class, $itemSet);
            Assert::type(\stdClass::class, $itemSet->weapon);
            Assert::type(\stdClass::class, $itemSet->armor);
            Assert::type(\stdClass::class, $itemSet->helmet);
        }
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["itemSet"]);
        Assert::type(\stdClass::class, $json["itemSet"]->weapon);
        Assert::type(\stdClass::class, $json["itemSet"]->armor);
        Assert::type(\stdClass::class, $json["itemSet"]->helmet);
        $expected = ["message" => "Item set with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new ItemSetsPresenterTest();
$test->run();
