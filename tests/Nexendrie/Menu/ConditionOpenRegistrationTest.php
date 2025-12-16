<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Nexendrie\Model\SettingsRepository;
use Nexendrie\Model\ThemesManager;
use Tester\Assert;
use Nexendrie\Model\TUserControl;

require __DIR__ . "/../../bootstrap.php";

final class ConditionOpenRegistrationTest extends \Tester\TestCase
{
    use TUserControl;

    public function testGetName(): void
    {
        /** @var ConditionOpenRegistration $condition */
        $condition = $this->getService(ConditionOpenRegistration::class);
        Assert::same("openRegistration", $condition->getName());
    }

    public function testIsAllowed(): void
    {
        /** @var ThemesManager $themeManager */
        $themeManager = $this->getService(ThemesManager::class);
        $condition = new ConditionOpenRegistration(
            new SettingsRepository(["registration" => ["open" => true,]], $themeManager)
        );
        Assert::same(true, $condition->isAllowed(true));
        Assert::same(false, $condition->isAllowed(false));
        $condition = new ConditionOpenRegistration(
            new SettingsRepository(["registration" => ["open" => false,]], $themeManager)
        );
        Assert::same(false, $condition->isAllowed(true));
        Assert::same(true, $condition->isAllowed(false));
    }
}

$test = new ConditionOpenRegistrationTest();
$test->run();
