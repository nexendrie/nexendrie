<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class TeamPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Team:default");
    $this->login();
    $this->checkAction(":Front:Team:default");
  }
}

$test = new TeamPresenterTest();
$test->run();
?>