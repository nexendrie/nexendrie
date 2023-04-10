<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Api extends AbstractMigration {
  public function change(): void {
    $this->table("users")
      ->addColumn("api", "boolean", ["default" => false,])
      ->update();
    $this->table("api_tokens")
      ->addColumn("token", "text")
      ->addColumn("user", "integer")
      ->addColumn("expire", "integer")
      ->addColumn("created", "integer")
      ->addForeignKey("user", "users")
      ->create();
  }
}
?>