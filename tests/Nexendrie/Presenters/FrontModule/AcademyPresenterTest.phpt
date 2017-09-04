<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

class AcademyPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkRedirect(":Front:Academy:default", "/user/login");
    $this->login();
    $this->checkAction(":Front:Academy:default");
  }
}

$test = new AcademyPresenterTest;
$test->run();
?>