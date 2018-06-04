<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ProfilePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testView() {
    Assert::exception(function() {
      $this->checkAction(":Front:Profile:default");
    }, BadRequestException::class);
    Assert::exception(function() {
      $this->checkAction(":Front:Profile:default", ["username" => "abc"]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Profile:default", ["username" => "system"]);
  }
  
  public function testArticles() {
    Assert::exception(function() {
      $this->checkAction(":Front:Profile:articles", ["username" => "abc"]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Profile:articles", ["username" => "system"]);
  }
  
  public function testSkills() {
    Assert::exception(function() {
      $this->checkAction(":Front:Profile:skills", ["username" => "abc"]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Profile:skills", ["username" => "system"]);
  }
}

$test = new ProfilePresenterTest();
$test->run();
?>