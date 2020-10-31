<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class RemoveInfomailsFromUser extends AbstractMigration {
  public function up(): void {
    $this->table("users")
      ->removeColumn("infomails")
      ->update();
  }

  public function down(): void {
    $this->table("users")
      ->addColumn("infomails", "boolean", ["default" => false, ])
      ->update();
  }
}
?>