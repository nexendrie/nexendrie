<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class PollsPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkPolls(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["polls"]);
        Assert::count($count, $json["polls"]);
        foreach ($json["polls"] as $poll) {
            Assert::type(\stdClass::class, $poll);
            Assert::type(\stdClass::class, $poll->author);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkPolls($response, 3);
        $response = $this->checkJson($action, ["associations" => ["users" => 1]]);
        $this->checkPolls($response, 3);
        $expected = ["message" => "User with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["poll"]);
        Assert::type(\stdClass::class, $json["poll"]->author);
        $expected = ["message" => "Poll with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new PollsPresenterTest();
$test->run();
