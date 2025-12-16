<?php
declare(strict_types=1);

namespace Nexendrie\Components;

/**
 * UserProfileLinkControlFactory
 *
 * @author Jakub Konečný
 */
interface UserProfileLinkControlFactory
{
    public function create(): UserProfileLinkControl;
}
