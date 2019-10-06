<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreatedUpdatedTime extends AbstractMigration {
  public function change() {
    $this->table("articles")
      ->renameColumn("added", "created")
      ->update();
    $this->table("comments")
      ->renameColumn("added", "created")
      ->update();
    $this->table("polls")
      ->renameColumn("added", "created")
      ->update();
    $this->table("beer_production")
      ->renameColumn("when", "created")
      ->update();
    $this->table("chat_messages")
      ->renameColumn("when", "created")
      ->update();
    $this->table("elections")
      ->renameColumn("when", "created")
      ->update();
    $this->table("monastery_donations")
      ->renameColumn("when", "created")
      ->update();
    $this->table("castles")
      ->renameColumn("founded", "created")
      ->update();
    $this->table("guilds")
      ->renameColumn("founded", "created")
      ->update();
    $this->table("monasteries")
      ->renameColumn("founded", "created")
      ->update();
    $this->table("orders")
      ->renameColumn("founded", "created")
      ->update();
    $this->table("towns")
      ->renameColumn("founded", "created")
      ->update();
    $this->table("deposits")
      ->renameColumn("opened", "created")
      ->update();
    $this->table("loans")
      ->renameColumn("taken", "created")
      ->update();
    $this->table("marriages")
      ->renameColumn("proposed", "created")
      ->update();
    $this->table("messages")
      ->renameColumn("sent", "created")
      ->update();
    $this->table("mounts")
      ->renameColumn("birth", "created")
      ->update();
    $this->table("poll_votes")
      ->renameColumn("voted", "created")
      ->update();
    $this->table("punishments")
      ->renameColumn("imprisoned", "created")
      ->update();
    $this->table("users")
      ->renameColumn("joined", "created")
      ->update();
    $this->table("user_adventures")
      ->renameColumn("started", "created")
      ->update();
    $this->table("user_jobs")
      ->renameColumn("started", "created")
      ->update();
  }
}
?>