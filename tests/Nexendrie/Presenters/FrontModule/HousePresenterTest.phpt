<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class HousePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    
    $this->login("premysl");
    $this->checkRedirect(":Front:House:default", "/");
    $this->login("jakub");
    $this->checkAction(":Front:House:default");
  }
  
  public function testBuy() {
    $this->checkRedirect(":Front:House:buy", "/user/login");
    $this->login();
    $this->checkRedirect(":Front:House:buy", "/");
    $this->login("jakub");
    $this->checkRedirect(":Front:House:buy", "/house");
  }
}

$test = new HousePresenterTest();
$test->run();
?>