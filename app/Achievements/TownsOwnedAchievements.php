<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * TownsOwnedAchievements
 *
 * @author Jakub Konečný
 */
class TownsOwnedAchievements extends BaseAchievement {
  protected $field = "townsOwned";
  protected $name = "Vládce";
  protected $description = "nexendrie.achievements.townsOwned";
  
  public function getRequirements(): array {
    return [1, 3, 8,];
  }
}
?>