<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface PrisonControlFactory
{
    public function create(): PrisonControl;
}
