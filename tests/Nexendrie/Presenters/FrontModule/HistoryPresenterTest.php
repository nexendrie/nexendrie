<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\HistoryControlFactory;

require __DIR__ . "/../../../bootstrap.php";

final class HistoryPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testDefault(): void
    {
        $this->checkAction(":Front:History:default");
        $this->login();
        $this->checkAction(":Front:History:default");
        /** @var HistoryControlFactory $factory */
        $factory = $this->getService(HistoryControlFactory::class);
        $component = $factory->create();
        $pages = $component->getPages();
        /** @var \Nexendrie\BookComponent\BookPage $page */
        foreach ($pages as $page) {
            $this->checkAction(":Front:Help:default", ["page" => $page->slug]);
            $this->login();
            $this->checkAction(":Front:Help:default", ["page" => $page->slug]);
        }
    }
}

$test = new HistoryPresenterTest();
$test->run();
