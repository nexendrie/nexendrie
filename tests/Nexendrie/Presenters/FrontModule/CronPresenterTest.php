<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class CronPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault(): void {
    /** @var \Nette\Application\Responses\TextResponse $response */ // @phpstan-ignore varTag.nativeType
    $response = $this->check(":Front:Cron:default");
    Assert::same(200, $this->getReturnCode());
    Assert::type(\Nette\Application\Responses\TextResponse::class, $response);
    Assert::type(\Nette\Application\UI\Template::class, $response->getSource());
  }
}

$test = new CronPresenterTest();
$test->run();
?>