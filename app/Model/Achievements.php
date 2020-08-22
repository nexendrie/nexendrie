<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Achievements\IAchievement;

/**
 * Achievements
 *
 * @author Jakub Konečný
 */
final class Achievements {
  use \Nette\SmartObject;
  
  /** @var IAchievement[] */
  protected array $achievements = [];

  /**
   * @param IAchievement[] $achievements
   */
  public function __construct(array $achievements) {
    $this->achievements = $achievements;
  }
  
  /**
   * @return IAchievement[]
   */
  public function getAllAchievements(): array {
    return $this->achievements;
  }
}
?>