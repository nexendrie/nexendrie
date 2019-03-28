<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class PollsPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Polls:default");
    $this->login();
    $this->checkAction(":Front:Polls:default");
  }
}

$test = new PollsPresenterTest();
$test->run();
?>