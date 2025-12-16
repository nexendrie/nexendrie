<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class AchievementsPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testDefault(): void
    {
        $this->checkRedirect(":Front:Achievements:default", "/user/login");
        $this->login("Rahym");
        $this->checkRedirect(":Front:Achievements:default", "/profile/Rahym/achievements");
    }
}

$test = new AchievementsPresenterTest();
$test->run();
