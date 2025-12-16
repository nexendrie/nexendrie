<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class SkillsPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkSkills(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["skills"]);
        Assert::count($count, $json["skills"]);
        foreach ($json["skills"] as $skill) {
            Assert::type(\stdClass::class, $skill);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkSkills($response, 10);
        $response = $this->checkJson($action, ["associations" => ["users" => 1]]);
        $this->checkSkills($response, 3);
        foreach ($response->getPayload()["skills"] as $skill) {
            Assert::type("int", $skill->level);
            Assert::type(\stdClass::class, $skill->skill);
        }
        $expected = ["message" => "User with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["skill"]);
        $expected = ["message" => "Skill with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new SkillsPresenterTest();
$test->run();
