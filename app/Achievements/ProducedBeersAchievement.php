<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * ProducedBeersAchievement
 *
 * @author Jakub Konečný
 */
final class ProducedBeersAchievement extends BaseAchievement {
  /** @var string */
  protected $field = "producedBeers";
  /** @var string */
  protected $name = "Pivař";
  /** @var string */
  protected $description = "nexendrie.achievements.producedBeers";
  
  public function getRequirements(): array {
    return [1, 8, 17, 32, ];
  }
}
?>