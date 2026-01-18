<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * PermissionDummy
 *
 * @author Jakub Konečný
 */
final readonly class PermissionDummy
{
    public int $id;
    public string $resource;
    public string $action;
    public int $group;

    public function __construct(Permission $p)
    {
        $this->id = $p->id;
        $this->resource = $p->resource;
        $this->action = $p->action;
        $this->group = $p->group->id;
    }
}
