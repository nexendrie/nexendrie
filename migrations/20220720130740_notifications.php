<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Notifications extends AbstractMigration {
  public function change(): void {
    $this->table("users")
      ->addColumn("notifications", "boolean", ["default" => false,])
      ->update();
    $this->table("notifications")
      ->addColumn("title", "text")
      ->addColumn("body", "text")
      ->addColumn("icon", "text", ["null" => true,])
      ->addColumn("tag", "text")
      ->addColumn("target_url", "text", ["null" => true,])
      ->addColumn("user", "integer")
      ->addColumn("created", "integer")
      ->addForeignKey("user", "users")
      ->create();
  }
}
?>