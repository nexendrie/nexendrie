<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CommentsModeration extends AbstractMigration {
  public function change(): void {
    $this->table("comments")
      ->addColumn("deleted", "boolean", ["default" => false, ])
      ->update();
    $this->table("content_reports")
      ->addColumn("comment", "integer")
      ->addColumn("user", "integer")
      ->addColumn("handled", "boolean")
      ->addColumn("created", "integer")
      ->addForeignKey("comment", "comments")
      ->addForeignKey("user", "users")
      ->create();
  }
}
?>