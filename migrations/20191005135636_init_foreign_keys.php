<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitForeignKeys extends AbstractMigration {
  public function change(): void {
    $this->table("towns")
      ->addForeignKey("owner", "users")
      ->update();
    $this->table("monasteries")
      ->addForeignKey("leader", "users")
      ->update();
  }
}
?>