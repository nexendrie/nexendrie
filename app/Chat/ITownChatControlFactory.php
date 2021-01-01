<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface ITownChatControlFactory extends IChatControlFactory {
  public function create(): TownChatControl;
}
?>