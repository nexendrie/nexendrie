<?php
declare(strict_types=1);

namespace Nexendrie\Components;

enum ElectionsState
{
    case Preparation;
    case Voting;
    case Results;
}
