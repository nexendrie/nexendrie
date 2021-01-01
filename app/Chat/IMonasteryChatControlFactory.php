<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface IMonasteryChatControlFactory extends IChatControlFactory {
  public function create(): MonasteryChatControl;
}
?>