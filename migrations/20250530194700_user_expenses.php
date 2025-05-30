<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserExpenses extends AbstractMigration {
  public function change(): void {
    $this->table("user_expenses")
      ->addColumn("category", "enum", ["values" => "castle_maintenance,house_maintenance,mount_maintenance", ])
      ->addColumn("amount", "integer", ["limit" => 3, ])
      ->addColumn("user", "integer")
      ->addColumn("created", "integer")
      ->addForeignKey("user", "users")
      ->create();
  }
}
?>