<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class TownsPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  protected function checkTowns(\Nette\Application\Responses\JsonResponse $response, int $count): void {
    $json = $response->getPayload();
    Assert::type("array", $json["towns"]);
    Assert::count($count, $json["towns"]);
    foreach($json["towns"] as $town) {
      Assert::type(\stdClass::class, $town);
      Assert::type(\stdClass::class, $town->owner);
      Assert::type("array", $town->denizens);
    }
  }
  
  public function testReadAll(): void {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $this->checkTowns($response, 11);
    $response = $this->checkJson($action, ["associations" => ["users" => 1]]);
    $this->checkTowns($response, 2);
    $expected = ["message" => "User with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
  }
  
  public function testRead(): void {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["town"]);
    Assert::type(\stdClass::class, $json["town"]->owner);
    Assert::type("array", $json["town"]->denizens);
    $expected = ["message" => "Town with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new TownsPresenterTest();
$test->run();
?>