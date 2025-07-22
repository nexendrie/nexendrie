<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class ItemsPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  protected function checkItems(\Nette\Application\Responses\JsonResponse $response, int $count): void {
    $json = $response->getPayload();
    Assert::type("array", $json["items"]);
    Assert::count($count, $json["items"]);
    foreach($json["items"] as $item) {
      Assert::type(\stdClass::class, $item);
    }
  }
  
  public function testReadAll() {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $this->checkItems($response, 44);
    $response = $this->checkJson($action, ["associations" => ["shops" => 1]]);
    $this->checkItems($response, 2);
    $expected = ["message" => "Shop with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["shops" => 50]]);
    $response = $this->checkJson($action, ["associations" => ["item-sets" => 1]]);
    $this->checkItems($response, 3);
    $expected = ["message" => "Item set with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["item-sets" => 50]]);
  }
  
  public function testRead() {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["item"]);
    $expected = ["message" => "Item with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new ItemsPresenterTest();
$test->run();
?>