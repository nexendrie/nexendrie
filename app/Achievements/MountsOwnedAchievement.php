<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * MountsOwnedAchievement
 *
 * @author Jakub Konečný
 */
final class MountsOwnedAchievement extends BaseAchievement {
  /** @var string */
  protected $field = "mountsOwned";
  /** @var string */
  protected $name = "Chovatel";
  /** @var string */
  protected $description = "nexendrie.achievements.mountsOwned";
  
  public function getRequirements(): array {
    return [1, 3, 8, 15,];
  }
}
?>