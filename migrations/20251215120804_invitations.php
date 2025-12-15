<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Invitations extends AbstractMigration {
  public function up(): void {
    $this->table("permissions")
      ->insert([
        [
          "id" => 26, "resource" => "user", "action" => "invite", "group" => 1, "created" => 0, "updated" => 0,
        ],
      ])
      ->update();
    $this->table("invitations")
      ->addColumn("email", "text", ["limit" => \Phinx\Db\Adapter\MysqlAdapter::TEXT_TINY,])
      ->addColumn("inviter", "integer")
      ->addColumn("created", "integer")
      ->addForeignKey("inviter", "users")
      ->save();
  }

  public function down(): void {
    $this->execute("DELETE FROM permissions WHERE id=26");
    $this->table("invitations")->drop()->save();
  }
}
?>