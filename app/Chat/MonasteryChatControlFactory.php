<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface MonasteryChatControlFactory extends ChatControlFactory
{
    public function create(): MonasteryChatControl;
}
