<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class HelpPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Help:default");
    $this->login();
    $this->checkAction(":Front:Help:default");
  }
}

$test = new HelpPresenterTest;
$test->run();
?>