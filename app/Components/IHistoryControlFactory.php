<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IHistoryControlFactory {
  public function create(): HistoryControl;
}
?>