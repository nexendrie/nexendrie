<?php
declare(strict_types=1);

namespace Nexendrie;

/**
 * @author Jakub Konečný
 */
interface ILocale {
  /**
   * Formats date and time
   * 
   * @param int $date
   * @return string
   */
  function formatDateTime(int $date);
  
  /**
   * Formats date
   * 
   * @param int $date
   * @return string
   */
  function formatDate(int $date);
  
  /**
   * Selects correct form according to $count
   * 
   * @param string $word1
   * @param string $word2
   * @param string $word3
   * @param int $count
   * @return string
   */
  function plural(string $word1, string $word2, string $word3, int $count);
  
  /**
   * @param int $amount
   * @return string
   */
  function money(int $amount);
  
  /**
   * @param int $amount
   * @return string
   */
  function hitpoints(int $amount);
}


namespace Nexendrie\Model;

/**
 * Locale Model
 * 
 * @author Jakub Konečný
 * @property array $formats
 */
class Locale implements \Nexendrie\ILocale {
  /** @var array */
  protected $formats = [];
  
  use \Nette\SmartObject;
  
  /**
   * @param array $formats
   */
  function __construct(array $formats) {
    $this->formats = $formats;
    $this->formats["plural"][1] = array_map(function($value) {
      return (int) $value;
    }, explode("-", $this->formats["plural"][1]));
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
   * @param string $word1
   * @param string $word2
   * @param string $word3
   * @param int $count
   * @return string
   */
  function plural(string $word1, string $word2, string $word3, int $count): string {
    $plural2 = $this->formats["plural"][1];
    if($count === $this->formats["plural"][0]) return $word1;
    elseif($count >= $plural2[0] AND $count <= $plural2[1]) return $word2;
    else return $word3;
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function money(int $amount): string {
    return "$amount " . $this->plural("groš", "groše", "grošů", $amount);
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function hitpoints(int $amount): string {
    return "$amount " . $this->plural("život", "životy", "životů", $amount);
  }
  
  function getFormats(): array {
    return $this->formats;
  }
}
?>