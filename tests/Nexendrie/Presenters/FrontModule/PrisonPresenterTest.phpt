<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class PrisonPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkRedirect(":Front:Prison:default", "/");
    $this->login();
    $this->checkRedirect(":Front:Prison:default", "/");
  }
}

$test = new PrisonPresenterTest();
$test->run();
?>