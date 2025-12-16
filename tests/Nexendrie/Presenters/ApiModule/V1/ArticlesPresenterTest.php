<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class ArticlesPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkArticles(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["articles"]);
        Assert::count($count, $json["articles"]);
        foreach ($json["articles"] as $article) {
            Assert::type(\stdClass::class, $article);
            Assert::type(\stdClass::class, $article->author);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkArticles($response, 16);
        $response = $this->checkJson($action, ["associations" => ["users" => 1]]);
        $this->checkArticles($response, 16);
        $expected = ["message" => "User with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["article"]);
        Assert::type(\stdClass::class, $json["article"]->author);
        $expected = ["message" => "Article with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new ArticlesPresenterTest();
$test->run();
