<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface IGuildChatControlFactory extends IChatControlFactory
{
    public function create(): GuildChatControl;
}
