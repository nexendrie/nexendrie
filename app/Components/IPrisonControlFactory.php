<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IPrisonControlFactory {
  public function create(): PrisonControl;
}
?>