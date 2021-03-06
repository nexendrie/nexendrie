<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface ITavernControlFactory {
  public function create(): TavernControl;
}
?>