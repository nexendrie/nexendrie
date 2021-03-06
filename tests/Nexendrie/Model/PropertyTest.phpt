<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class PropertyTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Property */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Property::class);
  }
  
  public function testBudget() {
    Assert::exception(function() {
      $this->model->budget();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->budget();
    Assert::type("array", $result);
    Assert::count(2, $result);
  }
}

$test = new PropertyTest();
$test->run();
?>