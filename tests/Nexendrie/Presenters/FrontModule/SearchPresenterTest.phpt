<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

use Nexendrie\Forms\SiteSearchFormFactory;

final class SearchPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function trySearchForm(array $data): void {
    $this->checkForm(":Front:Search:default", "siteSearchForm", $data, false);
  }
  
  public function testDefault() {
    $this->checkAction(":Front:Search:default");
    $formData = [
      "text" => "abc", "type" => SiteSearchFormFactory::TYPE_USERS,
    ];
    $this->trySearchForm($formData);
    $formData["text"] = "admin";
    $this->trySearchForm($formData);
    $formData["type"] = SiteSearchFormFactory::TYPE_ARTICLES_TITLES;
    $this->trySearchForm($formData);
    $formData["text"] = "nov";
    $this->trySearchForm($formData);
    $formData["type"] = SiteSearchFormFactory::TYPE_ARTICLES_TEXTS;
    $this->trySearchForm($formData);
    $formData["text"] = "abc";
    $this->trySearchForm($formData);
  }
}

$test = new SearchPresenterTest();
$test->run();
?>