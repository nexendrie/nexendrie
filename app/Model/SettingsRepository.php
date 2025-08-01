<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Utils\Arrays;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Nexendrie\Utils\Intervals;

/**
 * Settings Repository
 *
 * @author Jakub Konečný
 */
final class SettingsRepository {
  private array $defaults = [
    "roles" => [
      "guestRole" => 13,
      "loggedInRole" => 12,
      "bannedRole" => 14,
    ],
    "locale" => [
      "dateFormat" => "j.n.Y",
      "dateTimeFormat" => "j.n.Y G:i",
    ],
    "pagination" => [
      "articles" => 10,
    ],
    "newUser" => [
      "style" => "nexendrie",
      "money" => 30,
      "town" => 3,
    ],
    "fees" => [
      "incomeTax" => 10,
      "loanInterest" => 15,
      "buildMonastery" => 1000,
      "buildCastle" => 1500,
      "foundGuild" => 1000,
      "foundOrder" => 1200,
      "foundTown" => 1000,
      "autoFeedMount" => 8,
      "depositInterest" => 3,
      "buyHouse" => 500,
    ],
    "specialItems" => [
      "foundTown" => 15,
    ],
    "registration" => [
      "token" => "",
    ],
    "site" => [
      "versionSuffix" => "",
      "serverSideEventsCooldown" => 3,
    ],
    "features" => [
      "httpCaching" => false,
      "earlyHints" => false,
    ],
    "buildings" => [
      "weeklyWearingOut" => 3,
      "criticalCondition" => 30,
    ],
    "socialAccounts" => [
      "facebook" => "nexendrie",
      "twitter" => "nexendrieCZ",
      "friendica" => "@nexendrie@social.konecnyjakub.top",
    ],
  ];

  private array $rules = [
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
      "depositInterest" => "validatePercent",
    ],
    "socialAccounts" => [
      "friendica" => "validateFriendicaAccount",
    ],
    "site" => [
      "serverSideEventsCooldown" => "validateServerSideEventsCooldown",
    ],
  ];

  public readonly array $settings;
  
  public function __construct(array $settings, private readonly ThemesManager $themesManager) {
    $this->settings = $this->validateSettings($settings);
  }

  private function validateStyle(string $value): bool {
    return array_key_exists($value, $this->themesManager->getList());
  }

  private function validatePercent(int $value): bool {
    return Intervals::isInInterval($value, "[0,100]");
  }

  private function validateMoney(int $value): bool {
    return Intervals::isInInterval($value, "[1,100]");
  }

  private function validateFee(int $value): bool {
    return Intervals::isInInterval($value, "[0,5000]");
  }

  private function validateFriendicaAccount(string $value): bool {
    return (preg_match('/^@.+@.+/', $value) === 1);
  }

  private function validateServerSideEventsCooldown(int $value): bool {
    return Intervals::isInInterval($value, "[1,100]");
  }
  
  /**
   * Validate section $name of config
   */
  private function validateSection(string $name, array $config): array {
    $values = Arrays::get($config, $name, []);
    $resolver = new OptionsResolver();
    $defaults = $this->defaults[$name];
    $resolver->setDefaults($defaults);
    foreach($defaults as $key => $value) {
      $resolver->setAllowedTypes($key, gettype($value));
      if(isset($this->rules[$name][$key])) {
        $resolver->setAllowedValues($key, function($value) use($name, $key) {
          return $this->{$this->rules[$name][$key]}($value);
        });
      }
    }
    return $resolver->resolve($values);
  }

  private function validateSettings(array $settings): array {
    $return = [];
    $sections = array_keys($this->defaults);
    foreach($sections as $section) {
      $return[$section] = $this->validateSection($section, $settings);
    }
    return $return;
  }
}
?>