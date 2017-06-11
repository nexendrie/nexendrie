<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Utils\Arrays,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Nexendrie\Forms\UserSettingsFormFactory;

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
    ]
  ];
  
  /** @var array */
  protected $settings = [];
  
  use \Nette\SmartObject;
  
  function __construct(array $settings) {
    $this->settings = $this->validateSettings($settings);
  }
  
  /**
   * @param string $value
   * @return bool
   */
  protected function validateStyle($value): bool {
    return array_key_exists($value, UserSettingsFormFactory::getStylesList());
  }
  
  /**
   * @param int $value
   * @return bool
   */
  protected function validatePercent($value): bool {
    if(!is_int($value)) {
      return false;
    } elseif($value < 0 OR $value > 100) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * @param int $value
   * @return bool
   */
  protected function validateMoney($value): bool {
    if(!is_int($value)) {
      return false;
    } elseif($value < 1 OR $value > 100) {
      return false;
    }
    return true;
  }
  
  /**
   * @param int $value
   * @return bool
   */
  protected function validateFee($value): bool {
    if(!is_int($value)) {
      return false;
    } elseif($value < 0 OR $value > 5000) {
      return false;
    }
    return true;
  }
  
  /**
   * Validate section $name of config
   *
   * @param string $name
   * @param array $config
   * @return array
   */
  protected function validateSection(string $name, array $config): array {
    $values = Arrays::get($config, $name, []);
    $resolver = new OptionsResolver;
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
  
  /**
   * @param array $settings
   * @return array
   */
  protected function validateSettings(array $settings): array {
    $return = [];
    $sections = array_keys($this->defaults);
    foreach($sections as $section) {
      $return[$section] = $this->validateSection($section, $settings);
    }
    return $return;
  }
  
  /**
   * @return array
   */
  function getSettings(): array {
    return $this->settings;
  }
}
?>