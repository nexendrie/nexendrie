<?php
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
  function formatDateTime($date);
  
  /**
   * Formats date
   * 
   * @param int $date
   * @return string
   */
  function formatDate($date);
  
  /**
   * Selects correct form according to $count
   * 
   * @param string $word1
   * @param string $word2
   * @param string $word3
   * @param int $count
   * @return string
   */
  function plural($word1, $word2, $word3, $count);
  
  /**
   * @param int $amount
   * @return string
   */
  function money($amount);
  
  /**
   * @param int $amount
   * @return string
   */
  function hitpoints($amount);
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
  function formatDateTime($date) {
    return date($this->formats["dateTimeFormat"], $date);
  }
  
  /**
   * Formats date
   * 
   * @param int $date
   * @return string
   */
  function formatDate($date) {
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
  function plural($word1, $word2, $word3, $count) {
    $plural2 = $this->formats["plural"][1];
    if($count === $this->formats["plural"][0]) return $word1;
    elseif($count >= $plural2[0] AND $count <= $plural2[1]) return $word2;
    else return $word3;
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function money($amount) {
    return "$amount " . $this->plural("groš", "groše", "grošů", $amount);
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function hitpoints($amount) {
    return "$amount " . $this->plural("život", "životy", "životů", $amount);
  }
  
  function getFormats() {
    return $this->formats;
  }
}
?>