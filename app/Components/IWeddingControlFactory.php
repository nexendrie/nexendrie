<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IWeddingControlFactory
{
    public function create(): WeddingControl;
}
