<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface ElectionsControlFactory
{
    public function create(): ElectionsControl;
}
