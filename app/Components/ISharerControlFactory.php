<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface ISharerControlFactory
{
    public function create(): SharerControl;
}
