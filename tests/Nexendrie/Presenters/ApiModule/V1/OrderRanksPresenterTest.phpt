<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

final class OrderRanksPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  public function testReadAll() {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $json = $response->getPayload();
    Assert::type("array", $json["orderRanks"]);
    Assert::count(4, $json["orderRanks"]);
    foreach($json["orderRanks"] as $orderRank) {
      Assert::type(\stdClass::class, $orderRank);
    }
  }
  
  public function testRead() {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["orderRank"]);
    $expected = ["message" => "Order rank with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new OrderRanksPresenterTest();
$test->run();
?>