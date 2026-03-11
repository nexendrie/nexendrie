<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface HotReloadingControlFactory
{
    public function create(): HotReloadingControl;
}
