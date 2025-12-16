<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface TownChatControlFactory extends ChatControlFactory
{
    public function create(): TownChatControl;
}
