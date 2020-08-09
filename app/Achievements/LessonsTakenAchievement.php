<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * LessonsTakenAchievement
 *
 * @author Jakub Konečný
 */
final class LessonsTakenAchievement extends BaseAchievement {
  protected string $field = "lessonsTaken";
  protected string $name = "Student";
  protected string $description = "nexendrie.achievements.lessonsTaken";
  
  public function getRequirements(): array {
    return [1, 5, 10, 25, ];
  }
}
?>