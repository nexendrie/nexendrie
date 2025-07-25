<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Localization\Translator;
use Nette\Security\User;
use Nexendrie\Orm\User as UserEntity;

/**
 * Locale Model
 * 
 * @author Jakub Konečný
 */
final class Locale {
  public readonly array $formats;
  
  public function __construct(SettingsRepository $sr, private readonly Translator $translator, private readonly User $user) {
    $this->formats = $sr->settings["locale"];
  }
  
  /**
   * Formats date and time
   */
  public function formatDateTime(int $date): string {
    return date($this->formats["dateTimeFormat"], $date);
  }
  
  /**
   * Formats date
   */
  public function formatDate(int $date): string {
    return date($this->formats["dateFormat"], $date);
  }
  
  /**
   * Selects correct form according to $count
   */
  public function plural(string $message, int $count): string {
    return $this->translator->translate("nexendrie." . $message, $count);
  }
  
  public function money(int $amount): string {
    return $this->plural("money", $amount);
  }
  
  public function hitpoints(int $amount): string {
    return $this->plural("hitpoints", $amount);
  }
  
  public function barrels(int $amount): string {
    return $this->plural("barrels", $amount);
  }
  
  /**
   * @throws AuthenticationNeededException
   */
  public function genderMessage(string $message): string {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $pattern = [
      '#\(a\)#',
      '#([\(])([[:alpha:]]+)(\|)([[:alpha:]]+)(\))#u',
    ];
    $replace = [
      "", "",
    ];
    if($this->user->identity->gender === UserEntity::GENDER_MALE) {
      $replace[1] = "\$2";
    } elseif($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
      $replace = [
        "a", "\$4"
      ];
    }
    return (string) preg_replace($pattern, $replace, $message);
  }
}
?>