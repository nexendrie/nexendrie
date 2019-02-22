<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class AcademyPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkRedirect(":Front:Academy:default", "/user/login");
    $this->login();
    $this->checkAction(":Front:Academy:default");
  }

  public function testWork() {
    $this->checkRedirect(":Front:Academy:combat", "/user/login");
    $this->login();
    $this->checkAction(":Front:Academy:combat");
  }
}

$test = new AcademyPresenterTest();
$test->run();
?>