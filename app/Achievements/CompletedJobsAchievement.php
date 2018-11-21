<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * CompletedJobsAchievement
 *
 * @author Jakub Konečný
 */
final class CompletedJobsAchievement extends BaseAchievement {
  /** @var string */
  protected $field = "completedJobs";
  /** @var string */
  protected $name = "Pracant";
  /** @var string */
  protected $description = "nexendrie.achievements.completedJobs";
  
  public function getRequirements(): array {
    return [1, 4, 10, 24, ];
  }
}
?>