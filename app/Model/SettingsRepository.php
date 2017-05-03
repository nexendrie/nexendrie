<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Neon\Neon,
    Nette\Utils\FileSystem,
    Nette\IOException,
    Nette\Neon\Encoder,
    Nette\Utils\Arrays,
    Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Settings Repository
 *
 * @author Jakub Konečný
 * @property-read array $settings
 */
class SettingsRepository {
  /** @var array */
  protected $defaults = [
    "roles" => [
      "guestRole" => 13,
      "loggedInRole" => 12,
      "bannedRole" => 14
    ],
    "locale" => [
      "dateFormat" => "j.n.Y",
      "dateTimeFormat" => "j.n.Y G:i",
      "plural" => [
        0 => 1, "2-4", 5
      ]
    ],
    "pagination" => [
      "news" => 10
    ],
    "newUser" => [
      "style" => "blue-sky",
      "money" => 30,
      "town" => 3
    ],
    "fees" => [
      "incomeTax" => 10,
      "loanInterest" => 15,
      "buildMonastery" => 1000,
      "buildCastle" => 1500,
      "foundGuild" => 1000,
      "foundOrder" => 1200
    ],
    "registration" => [
      "token" => ""
    ],
    "site" => [
      "versionSuffix" => ""
    ]
  ];
  
  /** @var array */
  protected $settings = [];
  
  /** @var string */
  protected $appDir;
  
  use \Nette\SmartObject;
  
  function __construct(array $settings, string $appDir) {
    $this->settings = $this->validateSettings($settings);
    $this->appDir = $appDir;
  }
  
  /**
   * Validate section $name of config
   *
   * @param string $name
   * @param array $config
   * @return array
   */
  protected function validateSection(string $name, array $config) {
    $values = Arrays::get($config, $name, []);
    $resolver = new OptionsResolver;
    $defaults = $this->defaults[$name];
    $resolver->setDefaults($defaults);
    foreach($defaults as $key => $value) {
      $resolver->setAllowedTypes($key, gettype($value));
    }
    return $resolver->resolve($values);
  }
  
  /**
   * @param array $settings
   * @return array
   */
  protected function validateSettings(array $settings): array {
    return [
      "roles" => $this->validateSection("roles", $settings),
      "pagination" => $this->validateSection("pagination", $settings),
      "locale" => $this->validateSection("locale", $settings),
      "newUser" => $this->validateSection("newUser", $settings),
      "fees" => $this->validateSection("fees", $settings),
      "registration" => $this->validateSection("registration", $settings),
      "site" => $this->validateSection("site", $settings),
    ];
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
   * @throws IOException
   */
  function save(array $settings): void {
    $filename = $this->appDir . "/config/local.neon";
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