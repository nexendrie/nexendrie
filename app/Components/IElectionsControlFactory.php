<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IElectionsControlFactory {
  public function create(): ElectionsControl;
}
?>