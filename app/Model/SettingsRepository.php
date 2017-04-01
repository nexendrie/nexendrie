<?php
declare(strict_types=1);

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
class SettingsRepository {
  /** @var array */
  protected $settings = [];
  
  use \Nette\SmartObject;
  
  function __construct(array $settings) {
    $this->settings = $settings;
  }
  
  /**
   * @return array
   */
  function getSettings(): array {
    return $this->settings;
  }
  
  /**
   * Save new settings
   * 
   * @param array $settings
   * @return void
   */
  function save(array $settings): void {
    $filename = APP_DIR . "/config/local.neon";
    $config = Neon::decode(file_get_contents($filename));
    $config += ["nexendrie" => $settings];
    if(is_string($config["nexendrie"]["locale"]["plural"])) {
      $config["nexendrie"]["locale"]["plural"] = explode("\n", $config["nexendrie"]["locale"]["plural"]);
    }
    try {
      $content = Neon::encode($config, Encoder::BLOCK);
      FileSystem::write($filename, $content);
    } catch(IOException $e) {
      throw $e;
    }
  }
}
?>