<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class MarriagePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkRedirect(":Front:Marriage:default", "/user/login");
    $this->login();
    $this->checkAction(":Front:Marriage:default");
    $this->login("jakub");
    $this->checkAction(":Front:Marriage:default");
    $this->login("Rahym");
    $this->checkRedirect(":Front:Marriage:default", "/marriage/proposals");
  }
  
  public function testProposals() {
    $this->checkRedirect(":Front:Marriage:proposals", "/user/login");
    $this->login();
    $this->checkAction(":Front:Marriage:proposals");
  }
}
$test = new MarriagePresenterTest();
$test->run();
?>