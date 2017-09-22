<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class HistoryPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:History:default");
    $this->login();
    $this->checkAction(":Front:History:default");
  }
}

$test = new HistoryPresenterTest();
$test->run();
?>