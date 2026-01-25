<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class ChatPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testTown(): void
    {
        $this->checkRedirect(":Front:Chat:town", "/user/login");
        $this->login();
        $this->checkAction(":Front:Chat:town");
    }

    public function testMonastery(): void
    {
        $this->checkRedirect(":Front:Chat:monastery", "/user/login");
        $this->login();
        $this->checkRedirect(":Front:Chat:monastery", "/");
        $this->login("Rahym");
        $this->checkAction(":Front:Chat:monastery");
    }

    public function testOrder(): void
    {
        $this->checkRedirect(":Front:Chat:order", "/user/login");
        $this->login("Rahym");
        $this->checkRedirect(":Front:Chat:order", "/");
        $this->login();
        $this->checkAction(":Front:Chat:order");
    }

    public function testGuild(): void
    {
        $this->checkRedirect(":Front:Chat:guild", "/user/login");
        $this->login();
        $this->checkRedirect(":Front:Chat:guild", "/");
        $this->login("jakub");
        $this->checkAction(":Front:Chat:guild");
    }
}

$test = new ChatPresenterTest();
$test->run();
