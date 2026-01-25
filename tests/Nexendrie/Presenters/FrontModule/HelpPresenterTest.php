<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\HelpControlFactory;

require __DIR__ . "/../../../bootstrap.php";

final class HelpPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testDefault(): void
    {
        $this->checkAction(":Front:Help:default");
        $this->login();
        $this->checkAction(":Front:Help:default");
        /** @var HelpControlFactory $factory */
        $factory = $this->getService(HelpControlFactory::class);
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

$test = new HelpPresenterTest();
$test->run();
