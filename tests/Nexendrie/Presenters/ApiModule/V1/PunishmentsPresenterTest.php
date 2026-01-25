<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

final class PunishmentsPresenterTest extends \Tester\TestCase
{
    use TApiPresenter;

    protected function checkPunishments(\Nette\Application\Responses\JsonResponse $response, int $count): void
    {
        $json = $response->getPayload();
        Assert::type("array", $json["punishments"]);
        Assert::count($count, $json["punishments"]);
        foreach ($json["punishments"] as $punishment) {
            Assert::type(\stdClass::class, $punishment);
            Assert::type(\stdClass::class, $punishment->user);
        }
    }

    public function testReadAll(): void
    {
        $action = $this->getPresenterName() . ":readAll";
        $response = $this->checkJson($action);
        $this->checkPunishments($response, 1);
        $response = $this->checkJson($action, ["associations" => ["users" => 1]]);
        $this->checkPunishments($response, 0);
        $expected = ["message" => "User with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
    }

    public function testRead(): void
    {
        $action = $this->getPresenterName() . ":read";
        $response = $this->checkJson($action, ["id" => 1]);
        $json = $response->getPayload();
        Assert::type(\stdClass::class, $json["punishment"]);
        Assert::type(\stdClass::class, $json["punishment"]->user);
        $expected = ["message" => "Punishment with id 50 was not found."];
        $this->checkJsonScheme($action, $expected, ["id" => 50]);
    }
}

$test = new PunishmentsPresenterTest();
$test->run();
