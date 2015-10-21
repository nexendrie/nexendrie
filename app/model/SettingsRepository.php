<?php
namespace Nexendrie\Model;

use Nette\Neon\Neon,
    Nette\Utils\FileSystem,
    Nette\IOException,
    Nette\Neon\Encoder;

/**
 * Settings Repository
 *
 * @author Jakub Konečný
 * @property-read array $settings
 */
class SettingsRepository extends \Nette\Object {
  /** @var array */
  protected $settings = array();
  
  function __construct(array $settings) {
    $this->settings = $settings;
  }
  
  /**
   * @return array
   */
  function getSettings() {
    return $this->settings;
  }
  
  /**
   * Save new settings
   * 
   * @param array $settings
   * @return void
   */
  function save(array $settings) {
    $filename = APP_DIR . "/config/local.neon";
    $config = Neon::decode(file_get_contents($filename));
    $config += array("nexendrie" => $settings);
    try {
      $content = Neon::encode($config, Encoder::BLOCK);
      FileSystem::write($filename, $content);
    } catch (IOException $e) {
      throw $e;
    }
  }
}
?>