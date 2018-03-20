<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface ITownChatControlFactory {
  public function create(): TownChatControl;
}
?>