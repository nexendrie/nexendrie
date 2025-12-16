<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface HistoryControlFactory
{
    public function create(): HistoryControl;
}
