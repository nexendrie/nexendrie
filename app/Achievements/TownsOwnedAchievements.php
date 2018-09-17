<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * TownsOwnedAchievements
 *
 * @author Jakub Konečný
 */
final class TownsOwnedAchievements extends BaseAchievement {
  /** @var string */
  protected $field = "townsOwned";
  /** @var string */
  protected $name = "Vládce";
  /** @var string */
  protected $description = "nexendrie.achievements.townsOwned";
  
  public function getRequirements(): array {
    return [1, 3, 8,];
  }
}
?>