<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface GuildChatControlFactory extends ChatControlFactory
{
    public function create(): GuildChatControl;
}
