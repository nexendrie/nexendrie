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

  /**
   * @param IAchievement[] $achievements
   */
  public function __construct(public readonly array $achievements) {
  }
}
?>