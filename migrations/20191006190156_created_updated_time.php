<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreatedUpdatedTime extends AbstractMigration {
  public function change() {
    $this->table("articles")
      ->renameColumn("added", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("comments")
      ->renameColumn("added", "created")
      ->update();
    $this->table("polls")
      ->renameColumn("added", "created")
      ->addColumn("updated", "integer")
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
      ->addColumn("updated", "integer")
      ->update();
    $this->table("guilds")
      ->renameColumn("founded", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("monasteries")
      ->renameColumn("founded", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("orders")
      ->renameColumn("founded", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("towns")
      ->renameColumn("founded", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("deposits")
      ->renameColumn("opened", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("loans")
      ->renameColumn("taken", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("marriages")
      ->renameColumn("proposed", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("messages")
      ->renameColumn("sent", "created")
      ->update();
    $this->table("mounts")
      ->renameColumn("birth", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("poll_votes")
      ->renameColumn("voted", "created")
      ->update();
    $this->table("punishments")
      ->renameColumn("imprisoned", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("users")
      ->renameColumn("joined", "created")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("user_adventures")
      ->renameColumn("started", "created")
      ->update();
    $this->table("user_jobs")
      ->renameColumn("started", "created")
      ->update();
    $this->table("adventures")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("adventure_npcs")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("events")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("groups")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("guild_fees")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("guild_ranks")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("houses")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("items")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("item_sets")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("jobs")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("job_messages")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("meals")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("mount_types")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("order_fees")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("order_ranks")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("permissions")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("shops")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("skills")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("user_items")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
    $this->table("user_skills")
      ->addColumn("created", "integer")
      ->addColumn("updated", "integer")
      ->update();
  }
}
?>