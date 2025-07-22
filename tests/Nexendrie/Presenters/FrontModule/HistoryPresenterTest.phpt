<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class HistoryPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:History:default");
    $this->login();
    $this->checkAction(":Front:History:default");
    /** @var \Nexendrie\Components\HistoryControl $component */
    $component = $this->getService(\Nexendrie\Components\IHistoryControlFactory::class)->create();
    $pages = $component->getPages();
    /** @var \Nexendrie\BookComponent\BookPage $page */
    foreach($pages as $page) {
      $this->checkAction(":Front:Help:default", ["page" => $page->slug]);
      $this->login();
      $this->checkAction(":Front:Help:default", ["page" => $page->slug]);
    }
  }
}

$test = new HistoryPresenterTest();
$test->run();
?>