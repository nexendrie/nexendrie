<?php
namespace Nexendrie;

/**
 * @author Jakub Konečný
 */
class Locale extends \Nette\Object {
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
}
?>