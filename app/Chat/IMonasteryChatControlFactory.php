<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface IMonasteryChatControlFactory {
  public function create(): MonasteryChatControl;
}
?>