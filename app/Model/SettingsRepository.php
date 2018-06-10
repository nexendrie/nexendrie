<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Utils\Arrays;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Nexendrie\Forms\UserSettingsFormFactory;
use Nexendrie\Utils\Intervals;

/**
 * Settings Repository
 *
 * @author Jakub Konečný
 * @property-read array $settings
 */
final class SettingsRepository {
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
    ],
    "pagination" => [
      "news" => 10
    ],
    "newUser" => [
      "style" => "dark-sky",
      "money" => 30,
      "town" => 3
    ],
    "fees" => [
      "incomeTax" => 10,
      "loanInterest" => 15,
      "buildMonastery" => 1000,
      "buildCastle" => 1500,
      "foundGuild" => 1000,
      "foundOrder" => 1200,
      "autoFeedMount" => 8,
      "depositInterest" => 3,
    ],
    "registration" => [
      "token" => ""
    ],
    "site" => [
      "versionSuffix" => ""
    ]
  ];
  
  /** @var array */
  protected $rules = [
    "newUser" => [
      "style" => "validateStyle",
      "money" => "validateMoney",
    ],
    "fees" => [
      "incomeTax" => "validatePercent",
      "loanInterest" => "validatePercent",
      "buildMonastery" => "validateFee",
      "buildCastle" => "validateFee",
      "foundGuild" => "validateFee",
      "foundOrder" => "validateFee",
      "depositInterest" => "validatePercent"
    ]
  ];
  
  /** @var array */
  protected $settings = [];
  
  use \Nette\SmartObject;
  
  public function __construct(array $settings) {
    $this->settings = $this->validateSettings($settings);
  }
  
  protected function validateStyle(string $value): bool {
    return array_key_exists($value, UserSettingsFormFactory::getStylesList());
  }
  
  protected function validatePercent(int $value): bool {
    return Intervals::isInInterval($value, "[0,100]");
  }
  
  protected function validateMoney(int $value): bool {
    return Intervals::isInInterval($value, "[1,100]");
  }
  
  protected function validateFee(int $value): bool {
    return Intervals::isInInterval($value, "[0,5000]");
  }
  
  /**
   * Validate section $name of config
   */
  protected function validateSection(string $name, array $config): array {
    $values = Arrays::get($config, $name, []);
    $resolver = new OptionsResolver();
    $defaults = $this->defaults[$name];
    $resolver->setDefaults($defaults);
    foreach($defaults as $key => $value) {
      $resolver->setAllowedTypes($key, gettype($value));
      if(isset($this->rules[$name][$key])) {
        $resolver->setAllowedValues($key, function($value) use($name, $key) {
          return call_user_func([$this, $this->rules[$name][$key]], $value);
        });
      }
    }
    return $resolver->resolve($values);
  }
  
  protected function validateSettings(array $settings): array {
    $return = [];
    $sections = array_keys($this->defaults);
    foreach($sections as $section) {
      $return[$section] = $this->validateSection($section, $settings);
    }
    return $return;
  }
  
  public function getSettings(): array {
    return $this->settings;
  }
}
?>