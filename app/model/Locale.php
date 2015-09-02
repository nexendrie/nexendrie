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
}


namespace Nexendrie\Model;

/**
 * Locale Model
 * 
 * @author Jakub Konečný
 */
class Locale extends \Nette\Object implements \Nexendrie\ILocale {
  /** @var array */
  protected $formats = array();
  
  /**
   * @param array $formats
   */
  function __construct(array $formats) {
    $this->formats = $formats;
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
    if($count === $this->formats["plural"][0]) return $word1;
    elseif($count >= $this->formats["plural"][2]) return $word3;
    else return $word2;
  }
  
  /**
   * @param int $amount
   * @return string
   */
  function money($amount) {
    return "$amount " . $this->plural("groš", "groše", "grošů", $amount);
  }
  
  function getFormats() {
    return $this->formats;
  }
}
?>