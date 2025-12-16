<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IAdventureControlFactory
{
    public function create(): AdventureControl;
}
