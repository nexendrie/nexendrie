<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * CompletedJobsAchievement
 *
 * @author Jakub Konečný
 */
final class CompletedJobsAchievement extends BaseAchievement {
  protected string $field = "completedJobs";
  protected string $name = "Pracant";
  protected string $description = "nexendrie.achievements.completedJobs";
  
  public function getRequirements(): array {
    return [1, 4, 10, 24, ];
  }
}
?>