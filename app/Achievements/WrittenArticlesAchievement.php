<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * WrittenArticlesAchievement
 *
 * @author Jakub Konečný
 */
final class WrittenArticlesAchievement extends BaseAchievement {
  protected string $field = "writtenArticles";
  protected string $name = "Kronikář";
  protected string $description = "nexendrie.achievements.writtenArticles";

  public function getRequirements(): array {
    return [1, 5, 15, 34, ];
  }
}
?>