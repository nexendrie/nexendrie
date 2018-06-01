<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * LessonsTakenAchievement
 *
 * @author Jakub Konečný
 */
final class LessonsTakenAchievement extends BaseAchievement {
  protected $field = "lessonsTaken";
  protected $name = "Student";
  protected $description = "nexendrie.achievements.lessonsTaken";
  
  public function getRequirements(): array {
    return [1, 5, 10, 25,];
  }
}
?>