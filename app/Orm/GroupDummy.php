<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * GroupDummy
 *
 * @author Jakub Konečný
 */
final readonly class GroupDummy
{
    public int $id;
    public string $name;
    public string $singleName;
    public string $femaleName;
    public int $level;
    public string $path;
    public int $members;

    public function __construct(Group $g)
    {
        $this->id = $g->id;
        $this->name = $g->name;
        $this->singleName = $g->singleName;
        $this->femaleName = $g->femaleName;
        $this->level = $g->level;
        $this->path = $g->path;
        $this->members = $g->members->countStored();
    }
}
