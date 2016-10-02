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
class PermissionDummy {
  /** @var int */
  protected $id;
  /** @var string */
  protected $resource;
  /** @var string */
  protected $action;
  /** @var int */
  protected $group;
  
  use \Nette\SmartObject;
  
  function __construct(Permission $p) {
    $this->id = $p->id;
    $this->resource = $p->resource;
    $this->action = $p->action;
    $this->group = $p->group->id;
  }
  
  function getId(): int {
    return $this->id;
  }
  
  function getResource(): string {
    return $this->resource;
  }
  
  function getAction(): string {
    return $this->action;
  }
  
  function getGroup(): int {
    return $this->group;
  }
}
?>