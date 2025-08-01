<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Orm\Article;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class ArticlesPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testRedirects(): void {
    $this->checkRedirect(":Front:Articles:category", "/", ["category" => "abc"]);
    $this->checkRedirect(":Front:Articles:category", "/", ["category" => Article::CATEGORY_NEWS]);
    $this->checkRedirect(":Front:Articles:category", "/chronicle", ["category" => Article::CATEGORY_CHRONICLE]);
  }
  
  public function testDefault(): void {
    $this->checkAction(":Front:Articles:default");
  }
  
  public function testCategory(): void {
    $this->checkAction(":Front:Articles:category", ["category" => Article::CATEGORY_UNCATEGORIZED]);
  }
}

$test = new ArticlesPresenterTest();
$test->run();
?>