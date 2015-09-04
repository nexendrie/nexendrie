<?php
namespace Nexendrie\Model;

/**
 * Settings Repository
 *
 * @author Jakub Konečný
 */
class SettingsRepository extends \Nette\Object {
  /** @var array */
  protected $settings = array();
  
  function __construct(array $settings) {
    $this->settings = $settings;
  }
  
  function getSettings() {
    return $this->settings;
  }
}
?>