<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Localization\ITranslator;

/**
 * Locale Model
 * 
 * @author Jakub Konečný
 * @property array $formats
 */
class Locale {
  /** @var ITranslator */
  protected $translator;
  /** @var array */
  protected $formats = [];
  
  use \Nette\SmartObject;
  
  function __construct(SettingsRepository $sr, ITranslator $translator) {
    $this->translator = $translator;
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
  
  function getFormats(): array {
    return $this->formats;
  }
}
?>