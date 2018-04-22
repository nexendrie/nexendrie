<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * ProducedBeersAchievement
 *
 * @author Jakub Konečný
 */
class ProducedBeersAchievement extends BaseAchievement {
  protected $field = "producedBeers";
  protected $name = "Pivař";
  protected $description = "nexendrie.achievements.producedBeers";
  
  public function getRequirements(): array {
    return [1, 8, 17, 32,];
  }
}
?>