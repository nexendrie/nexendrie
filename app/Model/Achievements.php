<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Achievements\Achievement;

/**
 * Achievements
 *
 * @author Jakub Konečný
 */
final class Achievements
{
    /**
     * @param Achievement[] $achievements
     */
    public function __construct(public readonly array $achievements)
    {
    }
}
