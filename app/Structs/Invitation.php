<?php
declare(strict_types=1);

namespace Nexendrie\Structs;

use Nexendrie\Orm\User;

final class Invitation {
  public string $email = "";
  public User $inviter;
  public string $dt = "";
  public ?User $user = null;
}
?>