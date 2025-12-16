<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface ShopControlFactory
{
    public function create(): ShopControl;
}
