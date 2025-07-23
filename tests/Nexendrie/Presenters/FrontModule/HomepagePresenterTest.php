<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class HomepagePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testHomepage(): void {
    $this->checkAction(":Front:Homepage:page");
  }
}

$test = new HomepagePresenterTest();
$test->run();
?>