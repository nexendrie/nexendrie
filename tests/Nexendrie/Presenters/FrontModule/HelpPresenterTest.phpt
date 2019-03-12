<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class HelpPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Help:default");
    $this->login();
    $this->checkAction(":Front:Help:default");
    /** @var \Nexendrie\Components\HelpControl $component */
    $component = $this->getService(\Nexendrie\Components\IHelpControlFactory::class)->create();
    $pages = $component->getPages();
    /** @var \Nexendrie\BookComponent\BookPage $page */
    foreach($pages as $page) {
      $this->checkAction(":Front:Help:default", ["page" => $page->slug]);
      $this->login();
      $this->checkAction(":Front:Help:default", ["page" => $page->slug]);
    }
  }
}

$test = new HelpPresenterTest();
$test->run();
?>