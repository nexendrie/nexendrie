<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CommentsModeration extends AbstractMigration {
  public function change() {
    $this->table("comments")
      ->addColumn("deleted", "boolean", ["default" => false,])
      ->update();
  }
}
?>