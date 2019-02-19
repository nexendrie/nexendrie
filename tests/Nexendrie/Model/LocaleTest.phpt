<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class LocaleTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Locale */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Locale::class);
  }
  
  public function testFormatDateTime() {
    $result = $this->model->formatDateTime(time());
    Assert::type("string", $result);
  }
  
  public function testFormatDate() {
    $result = $this->model->formatDate(time());
    Assert::type("string", $result);
  }
  
  public function testMoney() {
    Assert::same("0 grošů", $this->model->money(0));
    Assert::same("1 groš", $this->model->money(1));
    Assert::same("2 groše", $this->model->money(2));
    Assert::same("3 groše", $this->model->money(3));
    Assert::same("4 groše", $this->model->money(4));
    Assert::same("5 grošů", $this->model->money(5));
  }
  
  public function testHitpoints() {
    Assert::same("0 životů", $this->model->hitpoints(0));
    Assert::same("1 život", $this->model->hitpoints(1));
    Assert::same("2 životy", $this->model->hitpoints(2));
    Assert::same("3 životy", $this->model->hitpoints(3));
    Assert::same("4 životy", $this->model->hitpoints(4));
    Assert::same("5 životů", $this->model->hitpoints(5));
  }
  
  public function testBarrels() {
    Assert::same("0 sudů", $this->model->barrels(0));
    Assert::same("1 sud", $this->model->barrels(1));
    Assert::same("2 sudy", $this->model->barrels(2));
    Assert::same("3 sudy", $this->model->barrels(3));
    Assert::same("4 sudy", $this->model->barrels(4));
    Assert::same("5 sudů", $this->model->barrels(5));
  }
  
  public function testGetFormats() {
    $formats = $this->model->formats;
    Assert::type("array", $formats);
    Assert::type("string", $formats["dateFormat"]);
    Assert::type("string", $formats["dateTimeFormat"]);
  }
  
  public function testGenderMessage() {
    Assert::exception(function() {
      $this->model->genderMessage("abc");
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::same("abcab", $this->model->genderMessage("abc(a)ab(a)"));
    Assert::same("abcý", $this->model->genderMessage("abc(ý|á)"));
    Assert::same("abcýss", $this->model->genderMessage("abc(ýss|ásd)"));
    $this->login("Světlana");
    Assert::same("abcásd", $this->model->genderMessage("abc(ýss|ásd)"));
  }
}

$test = new LocaleTest();
$test->run();
?>