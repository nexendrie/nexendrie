<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface IGuildChatControlFactory {
  public function create(): GuildChatControl;
}
?>