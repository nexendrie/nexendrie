<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class HousesPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkHouses(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["houses"]);
        Assert::count($count, $json["houses"]);
        foreach ($json["houses"] as $article) {
            Assert::type(\stdClass::class, $article);
            Assert::type(\stdClass::class, $article->owner);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkHouses($response, 1);
        $response = $this->checkJson($action, ["associations" => ["users" => 3]]);
        $this->checkHouses($response, 1);
        $expected = ["message" => "User with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["house"]);
        Assert::type(\stdClass::class, $json["house"]->owner);
        $expected = ["message" => "House with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new HousesPresenterTest();
$test->run();
