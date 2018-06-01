<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * MountsOwnedAchievement
 *
 * @author Jakub Konečný
 */
final class MountsOwnedAchievement extends BaseAchievement {
  protected $field = "mountsOwned";
  protected $name = "Chovatel";
  protected $description = "nexendrie.achievements.mountsOwned";
  
  public function getRequirements(): array {
    return [1, 3, 8, 15,];
  }
}
?>