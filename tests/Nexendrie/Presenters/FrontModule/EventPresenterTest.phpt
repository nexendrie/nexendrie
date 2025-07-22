<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class EventPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testView() {
    Assert::exception(function() {
      $this->checkAction(":Front:Event:view", ["id" => 50]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Event:view", ["id" => 1]);
  }
}

$test = new EventPresenterTest();
$test->run();
?>