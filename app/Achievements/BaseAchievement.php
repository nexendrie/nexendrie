<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

use Nexendrie\Orm\User;
use Nette\Localization\Translator;
use Nette\Utils\Arrays;

/**
 * BaseAchievement
 *
 * @author Jakub Konečný
 */
abstract class BaseAchievement implements IAchievement {
  use \Nette\SmartObject;

  protected string $field;
  protected string $name;
  protected string $description;
  
  public function __construct(protected Translator $translator) {
  }
  
  public function getName(): string {
    return $this->name;
  }
  
  public function getDescription(User $user): string {
    $currentLevel = $this->isAchieved($user);
    $message = $this->description;
    $newCount = Arrays::get($this->getRequirements(), $currentLevel, Arrays::get($this->getRequirements(), $this->getMaxLevel() - 1));
    $oldCount = $this->getProgress($user);
    return $this->translator->translate($message, 0, ["oldCount" => $oldCount, "newCount" => $newCount, ]);
  }
  
  public function getMaxLevel(): int {
    return count($this->getRequirements());
  }
  
  public function getProgress(User $user): int {
    return $user->{$this->field};
  }
  
  public function isAchieved(User $user): int {
    $count = $user->{$this->field};
    $requirements = $this->getRequirements();
    for($i = count($requirements) - 1; $i >= 0; $i--) {
      if($count >= $requirements[$i]) {
        return $i + 1;
      }
    }
    return 0;
  }
}
?>