<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Nexendrie\Model\SettingsRepository;

/**
 * OpenRegistrationCondition
 *
 * @author Jakub Konečný
 */
final class ConditionOpenRegistration extends BaseCondition
{
    public function __construct(private readonly SettingsRepository $sr)
    {
        $this->name = "openRegistration";
    }

    /**
     * @param bool $parameter
     * @throws \InvalidArgumentException
     */
    public function isAllowed($parameter = null): bool
    {
        if (!is_bool($parameter)) {
            throw new \InvalidArgumentException("Method " . __METHOD__ . " expects boolean as parameter.");
        }
        return $parameter === $this->sr->settings["registration"]["open"];
    }
}
