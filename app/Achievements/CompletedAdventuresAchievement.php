<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * CompletedAdventuresAchievement
 *
 * @author Jakub Konečný
 */
final class CompletedAdventuresAchievement extends BaseAchievement {
  /** @var string */
  protected $field = "completedAdventures";
  /** @var string */
  protected $name = "Dobrodruh";
  /** @var string */
  protected $description = "nexendrie.achievements.completedAdventures";
  
  public function getRequirements(): array {
    return [1, 5, 15, 34, ];
  }
}
?>