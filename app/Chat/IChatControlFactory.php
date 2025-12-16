<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use HeroesofAbenez\Chat\ChatControl;

interface IChatControlFactory
{
    public function create(): ChatControl;
}
