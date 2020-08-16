<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Nette\Utils\Finder;

final class ThemesManager {
  protected string $wwwDir;

  public function __construct(string $wwwDir) {
    $this->wwwDir = $wwwDir;
  }

  public function getList(): array {
    $styles = [];
    $dir = $this->wwwDir . "/styles";
    $file = file_get_contents("$dir/list.neon");
    if($file === false) {
      return [];
    }
    $list = Neon::decode($file);
    /** @var \SplFileInfo $style */
    foreach(Finder::findFiles("*.css")->in($dir) as $style) {
      $key = $style->getBasename(".css");
      $value = Arrays::get($list, $key, $key);
      $styles[$key] = $value;
    }
    return $styles;
  }
}
?>