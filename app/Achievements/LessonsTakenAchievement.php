<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * LessonsTakenAchievement
 *
 * @author Jakub Konečný
 */
final class LessonsTakenAchievement extends BaseAchievement {
  /** @var string */
  protected $field = "lessonsTaken";
  /** @var string */
  protected $name = "Student";
  /** @var string */
  protected $description = "nexendrie.achievements.lessonsTaken";
  
  public function getRequirements(): array {
    return [1, 5, 10, 25,];
  }
}
?>