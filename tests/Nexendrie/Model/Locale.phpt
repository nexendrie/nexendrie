<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

class LocaleTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Locale */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Locale::class);
  }
  
  function testFormatDateTime() {
    $result = $this->model->formatDateTime(time());
    Assert::type("string", $result);
  }
  
  function testFormatDate() {
    $result = $this->model->formatDate(time());
    Assert::type("string", $result);
  }
  
  function testMoney() {
    Assert::same("0 grošů", $this->model->money(0));
    Assert::same("1 groš", $this->model->money(1));
    Assert::same("2 groše", $this->model->money(2));
    Assert::same("3 groše", $this->model->money(3));
    Assert::same("4 groše", $this->model->money(4));
    Assert::same("5 grošů", $this->model->money(5));
  }
  
  function testHitpoints() {
    Assert::same("0 životů", $this->model->hitpoints(0));
    Assert::same("1 život", $this->model->hitpoints(1));
    Assert::same("2 životy", $this->model->hitpoints(2));
    Assert::same("3 životy", $this->model->hitpoints(3));
    Assert::same("4 životy", $this->model->hitpoints(4));
    Assert::same("5 životů", $this->model->hitpoints(5));
  }
  
  function testGetFormats() {
    $formats = $this->model->formats;
    Assert::type("array", $formats);
    Assert::type("string", $formats["dateFormat"]);
    Assert::type("string", $formats["dateTimeFormat"]);
    Assert::type("array", $formats["plural"]);
    Assert::count(3, $formats["plural"]);
  }
}

$test = new LocaleTest;
$test->run();
?>