<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class MessagesPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkRedirect($action, "/user/login");
    $this->login();
    $this->checkAction($action);
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Messages:default");
  }
  
  public function testSent() {
    $this->defaultChecks(":Front:Messages:sent");
  }
  
  public function testView() {
    $this->checkRedirect(":Front:Messages:view", "/user/login", ["id" => 5000]);
    $this->login("jakub");
    Assert::exception(function() {
      $this->check(":Front:Messages:view", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkForward(":Front:Messages:view", "Front:Messages:cannotshow", ["id" => 1]);
    $this->login();
    $this->checkAction(":Front:Messages:view", ["id" => 1]);
  }
  
  public function testNew() {
    $this->defaultChecks(":Front:Messages:new");
  }
}

$test = new MessagesPresenterTest();
$test->run();
?>