<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * CompletedJobsAchievement
 *
 * @author Jakub Konečný
 */
final class CompletedJobsAchievement extends BaseAchievement {
  protected $field = "completedJobs";
  protected $name = "Pracant";
  protected $description = "nexendrie.achievements.completedJobs";
  
  public function getRequirements(): array {
    return [1, 4, 10, 24,];
  }
}
?>