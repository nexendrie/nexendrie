<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * PermissionDummy
 *
 * @author Jakub Konečný
 */
final class PermissionDummy {
  public readonly int $id;
  public readonly string $resource;
  public readonly string $action;
  public readonly int $group;
  
  use \Nette\SmartObject;
  
  public function __construct(Permission $p) {
    $this->id = $p->id;
    $this->resource = $p->resource;
    $this->action = $p->action;
    $this->group = $p->group->id;
  }
}
?>