<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

/**
 * WrittenArticlesAchievement
 *
 * @author Jakub Konečný
 */
final class WrittenArticlesAchievement extends BaseAchievement {
  /** @var string */
  protected $field = "writtenArticles";
  /** @var string */
  protected $name = "Kronikář";
  /** @var string */
  protected $description = "nexendrie.achievements.writtenArticles";

  public function getRequirements(): array {
    return [1, 5, 15, 34, ];
  }
}
?>