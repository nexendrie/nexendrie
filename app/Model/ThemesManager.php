<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Nette\Utils\Finder;

final class ThemesManager {
  public const SUFFIX_DEPRECATED = " (zavržený)";
  public const SUFFIX_EXPERIMENTAL = " (experimentální)";
  private const KEY_DEPRECATED = "deprecated";
  private const KEY_EXPERIMENTAL = "experimental";
  private const KEY_NAME = "name";
  private const THEMES_DIR = "styles";

  public function __construct(private readonly string $wwwDir) {
  }

  public function getList(): array {
    $styles = [];
    $dir = $this->wwwDir . "/" . self::THEMES_DIR;
    $file = file_get_contents("$dir/list.neon");
    if($file === false) {
      return [];
    }
    /** @var array<string, array{name: string, deprecated?: bool, experimental?: bool}> $list */
    $list = Neon::decode($file);
    /** @var \SplFileInfo $style */
    foreach(Finder::findFiles("*.css")->in($dir) as $style) {
      $key = $style->getBasename(".css");
      $value = Arrays::get($list, $key, $key);
      if (!is_array($value)) {
        $styles[$key] = $key;
      } else {
        $name = (string) Arrays::get($value, self::KEY_NAME, "");
        $deprecated = (bool) Arrays::get($value, self::KEY_DEPRECATED, false);
        $experimental = (bool) Arrays::get($value, self::KEY_EXPERIMENTAL, false);
        if($deprecated) {
          $name .= self::SUFFIX_DEPRECATED;
        } elseif($experimental) {
          $name .= self::SUFFIX_EXPERIMENTAL;
        }
        $styles[$key] = $name;
      }
    }
    return $styles;
  }

  public function getThemeFileUrl(string $themeName): string {
    $baseUrl = dirname($_SERVER["PHP_SELF"]);
    $baseUrl = preg_replace("$^/www$", "/", $baseUrl);
    return $baseUrl . self::THEMES_DIR . "/" . $themeName . ".css";
  }
}
?>