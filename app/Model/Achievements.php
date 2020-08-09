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
  
  public function __construct(\Nette\DI\Container $container) {
    $services = $container->findByType(IAchievement::class);
    foreach($services as $service) {
      $this->achievements[] = $container->getService($service);
    }
  }
  
  /**
   * @return IAchievement[]
   */
  public function getAllAchievements(): array {
    return $this->achievements;
  }
}
?>