<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * ProducedBeersAchievement
 *
 * @author Jakub Konečný
 */
final class ProducedBeersAchievement extends BaseAchievement
{
    protected string $field = "producedBeers";
    protected string $name = "Pivař";
    protected string $description = "nexendrie.achievements.producedBeers";

    public function getRequirements(): array
    {
        return [1, 8, 17, 32,];
    }
}
