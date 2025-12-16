<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface PollControlFactory
{
    public function create(): PollControl;
}
