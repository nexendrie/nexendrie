<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ProfilePresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testView(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Profile:default");
        }, BadRequestException::class);
        Assert::exception(function () {
            $this->checkAction(":Front:Profile:default", ["name" => "abc"]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Profile:default", ["name" => "Vladěna"]);
    }

    public function testArticles(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Profile:articles", ["name" => "abc"]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Profile:articles", ["name" => "Vladěna"]);
    }

    public function testSkills(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Profile:skills", ["name" => "abc"]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Profile:skills", ["name" => "Vladěna"]);
    }

    public function testAchievements(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Profile:achievements", ["name" => "abc"]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Profile:achievements", ["name" => "Vladěna"]);
    }

    public function testComments(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Profile:comments", ["name" => "abc"]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Profile:comments", ["name" => "Vladěna"]);
    }
}

$test = new ProfilePresenterTest();
$test->run();
