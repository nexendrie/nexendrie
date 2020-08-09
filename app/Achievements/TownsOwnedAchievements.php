<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * TownsOwnedAchievements
 *
 * @author Jakub Konečný
 */
final class TownsOwnedAchievements extends BaseAchievement {
  protected string $field = "townsOwned";
  protected string $name = "Vládce";
  protected string $description = "nexendrie.achievements.townsOwned";
  
  public function getRequirements(): array {
    return [1, 3, 8, ];
  }
}
?>