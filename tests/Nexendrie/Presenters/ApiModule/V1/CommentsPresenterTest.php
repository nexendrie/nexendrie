<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class CommentsPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkComments(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["comments"]);
        Assert::count($count, $json["comments"]);
        foreach ($json["comments"] as $comment) {
            Assert::type(\stdClass::class, $comment);
            Assert::type(\stdClass::class, $comment->article);
            Assert::type(\stdClass::class, $comment->author);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkComments($response, 5);
        $response = $this->checkJson($action, ["associations" => ["users" => 1]]);
        $this->checkComments($response, 5);
        $response = $this->checkJson($action, ["associations" => ["articles" => 1]]);
        $this->checkComments($response, 1);
        $expected = ["message" => "User with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
        $expected = ["message" => "Article with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["articles" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["comment"]);
        Assert::type(\stdClass::class, $json["comment"]->article);
        Assert::type(\stdClass::class, $json["comment"]->author);
        $expected = ["message" => "Comment with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new CommentsPresenterTest();
$test->run();
