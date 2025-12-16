<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface AdventureControlFactory
{
    public function create(): AdventureControl;
}
