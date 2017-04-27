<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";


class SettingsRepositoryTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var SettingsRepository */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(SettingsRepository::class);
  }
  
  function testGetSettings() {
    $result = $this->model->settings;
    Assert::type("array", $result);
  }
}

$test = new SettingsRepositoryTest;
$test->run();
?>