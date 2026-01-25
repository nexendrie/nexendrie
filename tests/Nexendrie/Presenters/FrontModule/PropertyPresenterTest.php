<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class PropertyPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    protected function defaultChecks(string $action): void
    {
        $this->checkRedirect($action, "/user/login");
        $this->login();
        $this->checkAction($action);
    }

    public function testDefault(): void
    {
        $this->defaultChecks(":Front:Property:default");
    }

    public function testBudget(): void
    {
        $this->defaultChecks(":Front:Property:budget");
    }

    public function testEquipment(): void
    {
        $this->defaultChecks(":Front:Property:equipment");
    }

    public function testPotions(): void
    {
        $this->defaultChecks(":Front:Property:potions");
    }

    public function testTown(): void
    {
        $this->checkRedirect(":Front:Property:town", "/user/login", ["id" => 5000]);
        $this->login("jakub");
        $this->checkRedirect(":Front:Property:town", "/", ["id" => 5000]);
        $this->checkRedirect(":Front:Property:town", "/", ["id" => 1]);
        $this->login();
        $this->checkAction(":Front:Property:town", ["id" => 1]);
        $this->checkAction(":Front:Property:town", ["id" => 2]);
    }
}

$test = new PropertyPresenterTest();
$test->run();
