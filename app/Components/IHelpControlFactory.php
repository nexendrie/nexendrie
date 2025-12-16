<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IHelpControlFactory
{
    public function create(): HelpControl;
}
