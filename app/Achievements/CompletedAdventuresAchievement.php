<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * CompletedAdventuresAchievement
 *
 * @author Jakub Konečný
 */
final class CompletedAdventuresAchievement extends BaseAchievement {
  protected string $field = "completedAdventures";
  protected string $name = "Dobrodruh";
  protected string $description = "nexendrie.achievements.completedAdventures";
  
  public function getRequirements(): array {
    return [1, 5, 15, 34, ];
  }
}
?>