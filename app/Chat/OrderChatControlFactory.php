<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface OrderChatControlFactory extends ChatControlFactory
{
    public function create(): OrderChatControl;
}
