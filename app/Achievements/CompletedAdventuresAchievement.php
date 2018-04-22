<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * CompletedAdventuresAchievement
 *
 * @author Jakub Konečný
 */
class CompletedAdventuresAchievement extends BaseAchievement {
  protected $field = "completedAdventures";
  protected $name = "Dobrodruh";
  protected $description = "nexendrie.achievements.completedAdventures";
  
  public function getRequirements(): array {
    return [1, 5, 15, 34,];
  }
}
?>