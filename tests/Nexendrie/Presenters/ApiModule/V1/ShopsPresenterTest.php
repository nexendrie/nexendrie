<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class ShopsPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  public function testReadAll(): void {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $json = $response->getPayload();
    Assert::type("array", $json["shops"]);
    Assert::count(6, $json["shops"]);
    foreach($json["shops"] as $shop) {
      Assert::type(\stdClass::class, $shop);
      Assert::type("array", $shop->items);
    }
  }
  
  public function testRead(): void {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["shop"]);
    Assert::type("array", $json["shop"]->items);
    $expected = ["message" => "Shop with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new ShopsPresenterTest();
$test->run();
?>