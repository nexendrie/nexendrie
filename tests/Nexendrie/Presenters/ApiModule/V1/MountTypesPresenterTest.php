<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class MountTypesPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  public function testReadAll(): void {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $json = $response->getPayload();
    Assert::type("array", $json["mountTypes"]);
    Assert::count(5, $json["mountTypes"]);
    foreach($json["mountTypes"] as $mountType) {
      Assert::type(\stdClass::class, $mountType);
    }
  }
  
  public function testRead(): void {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["mountType"]);
    $expected = ["message" => "Mount type with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new MountTypesPresenterTest();
$test->run();
?>