<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * PermissionDummy
 *
 * @author Jakub Konečný
 * @property-read int $id
 * @property-read string $resource
 * @property-read string $action
 * @property-read int $group
 */
final class PermissionDummy {
  protected int $id;
  protected string $resource;
  protected string $action;
  protected int $group;
  
  use \Nette\SmartObject;
  
  public function __construct(Permission $p) {
    $this->id = $p->id;
    $this->resource = $p->resource;
    $this->action = $p->action;
    $this->group = $p->group->id;
  }
  
  public function getId(): int {
    return $this->id;
  }
  
  public function getResource(): string {
    return $this->resource;
  }
  
  public function getAction(): string {
    return $this->action;
  }
  
  public function getGroup(): int {
    return $this->group;
  }
}
?>