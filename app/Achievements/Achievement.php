<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

use Nexendrie\Orm\User;

/**
 * Achievement
 *
 * @author Jakub Konečný
 */
interface Achievement
{
    public function getName(): string;

    public function getDescription(User $user): string;

    public function getMaxLevel(): int;

    /**
     * @return int[]
     */
    public function getRequirements(): array;

    public function getProgress(User $user): int;

    /**
     * @return int Achieved level or 0
     */
    public function isAchieved(User $user): int;
}
