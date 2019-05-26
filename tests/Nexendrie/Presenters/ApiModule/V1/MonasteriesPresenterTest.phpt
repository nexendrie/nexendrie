<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

final class MonasteriesPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  protected function checkMonasteries(\Nette\Application\Responses\JsonResponse $response, int $count): void {
    $json = $response->getPayload();
    Assert::type("array", $json["monasteries"]);
    Assert::count($count, $json["monasteries"]);
    foreach($json["monasteries"] as $monastery) {
      Assert::type(\stdClass::class, $monastery);
      Assert::type(\stdClass::class, $monastery->leader);
      Assert::type(\stdClass::class, $monastery->town);
      Assert::type("array", $monastery->members);
    }
  }
  
  public function testReadAll() {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $this->checkMonasteries($response, 2);
    $response = $this->checkJson($action, ["associations" => ["towns" => 1]]);
    $this->checkMonasteries($response, 1);
    $expected = ["message" => "Town with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["towns" => 50]]);
  }
  
  public function testRead() {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["monastery"]);
    Assert::type(\stdClass::class, $json["monastery"]->leader);
    Assert::type(\stdClass::class, $json["monastery"]->town);
    Assert::type("array", $json["monastery"]->members);
    $expected = ["message" => "Monastery with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new MonasteriesPresenterTest();
$test->run();
?>