<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class WorkPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkRedirect(":Front:Work:default", "/user/login");
    $this->login();
    $this->checkRedirect(":Front:Work:default", "/work/offers");
  }
  
  public function testOffers() {
    $this->checkRedirect(":Front:Work:offers", "/user/login");
    $this->login();
    $this->checkAction(":Front:Work:offers");
  }
}
$test = new WorkPresenterTest();
$test->run();
?>