<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";


class PropertyTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \TUserControl;
  
  /** @var Property */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Property::class);
  }
  
  function testBudget() {
    Assert::exception(function() {
      $this->model->budget();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->budget();
    Assert::type("array", $result);
    Assert::count(2, $result);
  }
}

$test = new PropertyTest;
$test->run();
?>