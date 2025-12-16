<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IFaviconControlFactory
{
    public function create(): FaviconControl;
}
