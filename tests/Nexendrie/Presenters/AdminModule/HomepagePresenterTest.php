<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class HomepagePresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault(): void {
    $this->defaultChecks(":Admin:Homepage:default");
  }
}

$test = new HomepagePresenterTest();
$test->run();
?>