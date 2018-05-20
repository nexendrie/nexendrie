<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IAcademyControlFactory {
  public function create(): AcademyControl;
}
?>