<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Localization\ITranslator,
    Nette\Security\User,
    Nexendrie\Orm\User as UserEntity;

/**
 * Locale Model
 * 
 * @author Jakub Konečný
 * @property array $formats
 */
class Locale {
  /** @var ITranslator */
  protected $translator;
  /** @var User */
  protected $user;
  /** @var array */
  protected $formats = [];
  
  use \Nette\SmartObject;
  
  function __construct(SettingsRepository $sr, ITranslator $translator, User $user) {
    $this->translator = $translator;
    $this->user = $user;
    $this->formats = $sr->settings["locale"];
  }
  
  /**
   * Formats date and time
   * 
   * @param int $date
   * @return string
   */
  function formatDateTime(int $date): string {
    return date($this->formats["dateTimeFormat"], $date);
  }
  
  /**
   * Formats date
   * 
   * @param int $date
   * @return string
   */
  function formatDate(int $date): string {
    return date($this->formats["dateFormat"], $date);
  }
  
  /**
   * Selects correct form according to $count
   * 
   * @param string $message
   * @param int $count
   * @return string
   */
  function plural(string $message, int $count): string {
    return $this->translator->translate("nexendrie." . $message, $count);
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function money(int $amount): string {
    return $this->plural("money", $amount);
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function hitpoints(int $amount): string {
    return $this->plural("hitpoints", $amount);
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function barrels(int $amount): string {
    return $this->plural("barrels", $amount);
  }
  
  /**
   * @param $message
   * @return string
   * @throws AuthenticationNeededException
   */
  function genderMessage($message): string {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
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
    return preg_replace($pattern, $replace, $message);
  }
  
  function getFormats(): array {
    return $this->formats;
  }
}
?>