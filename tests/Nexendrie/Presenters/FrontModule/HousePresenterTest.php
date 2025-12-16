<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class HousePresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testDefault(): void
    {
        $this->checkRedirect(":Front:House:default", "/user/login");
        $this->login();
        $this->checkRedirect(":Front:House:default", "/");
        $this->login("premysl");
        $this->checkRedirect(":Front:House:default", "/");
        $this->login("jakub");
        $this->checkAction(":Front:House:default");
    }

    public function testBuy(): void
    {
        $this->checkRedirect(":Front:House:buy", "/user/login");
        $this->login();
        $this->checkRedirect(":Front:House:buy", "/");
        $this->login("jakub");
        $this->checkRedirect(":Front:House:buy", "/house");
        $this->login("premysl");
        $this->modifyUser(["money" => 1], function () {
            $this->checkRedirect(":Front:House:buy", "/");
        });
    }

    public function testSignalUpgrade(): void
    {
        $this->checkSignal(":Front:House:default", "upgrade", [], [], "/user/login");
        $this->login();
        $this->checkSignal(":Front:House:default", "upgrade", [], [], "/");
        $this->login("premysl");
        $this->checkSignal(":Front:House:default", "upgrade", [], [], "/house");
        $this->login("jakub");
        $this->checkSignal(":Front:House:default", "upgrade", [], [], "/house");
    }

    public function testSignalRepair(): void
    {
        $this->checkSignal(":Front:House:default", "repair", [], [], "/user/login");
        $this->login();
        $this->checkSignal(":Front:House:default", "repair", [], [], "/");
        $this->login("premysl");
        $this->checkSignal(":Front:House:default", "repair", [], [], "/house");
        $this->login("jakub");
        $this->checkSignal(":Front:House:default", "repair", [], [], "/house");
    }

    public function testSignalUpgradeBrewery(): void
    {
        $this->checkSignal(":Front:House:default", "upgradeBrewery", [], [], "/user/login");
        $this->login();
        $this->checkSignal(":Front:House:default", "upgradeBrewery", [], [], "/");
        $this->login("premysl");
        $this->checkSignal(":Front:House:default", "upgradeBrewery", [], [], "/house");
        $this->login("jakub");
        $this->checkSignal(":Front:House:default", "upgradeBrewery", [], [], "/house");
    }

    public function testSignalProduceBeer(): void
    {
        $this->checkSignal(":Front:House:default", "produceBeer", [], [], "/user/login");
        $this->login();
        $this->checkSignal(":Front:House:default", "produceBeer", [], [], "/");
        $this->login("premysl");
        $this->checkSignal(":Front:House:default", "produceBeer", [], [], "/house");
    }
}

$test = new HousePresenterTest();
$test->run();
