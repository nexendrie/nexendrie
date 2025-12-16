<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface TavernControlFactory
{
    public function create(): TavernControl;
}
