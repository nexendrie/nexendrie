<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IPollControlFactory {
  public function create(): PollControl;
}
?>