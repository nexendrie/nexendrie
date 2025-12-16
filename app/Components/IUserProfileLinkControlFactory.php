<?php
declare(strict_types=1);

namespace Nexendrie\Components;

/**
 * IUserProfileLinkControlFactory
 *
 * @author Jakub Konečný
 */
interface IUserProfileLinkControlFactory
{
    public function create(): UserProfileLinkControl;
}
