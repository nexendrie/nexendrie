<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Init extends AbstractMigration {
  public function change(): void {
    $this->table("events")
      ->addColumn("name", "string", ["limit" => 20,])
      ->addColumn("description", "text")
      ->addColumn("start", "integer")
      ->addColumn("end", "integer")
      ->addColumn("adventures_bonus", "integer", ["limit" => 3, "default" => 0,])
      ->addColumn("work_bonus", "integer", ["limit" => 3, "default" => 0,])
      ->addColumn("prayer_life_bonus", "integer", ["limit" => 3, "default" => 0,])
      ->addColumn("training_discount", "integer", ["limit" => 3, "default" => 0,])
      ->addColumn("repairing_discount", "integer", ["limit" => 3, "default" => 0,])
      ->addColumn("shopping_discount", "integer", ["limit" => 3, "default" => 0,])
      ->create();
    $this->table("adventures")
      ->addColumn("name", "string", ["limit" => 20,])
      ->addColumn("description", "text")
      ->addColumn("intro", "text")
      ->addColumn("epilogue", "text")
      ->addColumn("level", "integer", ["limit" => 5, "default" => 50,])
      ->addColumn("reward", "integer", ["limit" => 3,])
      ->addColumn("event", "integer", ["null" => true,])
      ->addForeignKey("event", "events")
      ->create();
    $this->table("adventure_npcs")
      ->addColumn("name", "string", ["limit" => 15,])
      ->addColumn("adventure", "integer")
      ->addColumn("order", "integer", ["limit" => 1,])
      ->addColumn("hitpoints", "integer", ["limit" => 3,])
      ->addColumn("strength", "integer", ["limit" => 2,])
      ->addColumn("armor", "integer", ["limit" => 2,])
      ->addColumn("initiative", "integer", ["limit" => 2, "default" => 0,])
      ->addColumn("reward", "integer", ["limit" => 3,])
      ->addColumn("encounter_text", "text")
      ->addColumn("victory_text", "text")
      ->addForeignKey("adventure", "adventures")
      ->create();
    $this->table("groups")
      ->addColumn("name", "string", ["limit" => 30,])
      ->addColumn("single_name", "string", ["limit" => 30,])
      ->addColumn("female_name", "string", ["limit" => 30,])
      ->addColumn("level", "integer", ["limit" => 5,])
      ->addColumn("path", "enum", ["values" => "city,church,tower",])
      ->addColumn("max_loan", "integer", ["default" => 0,])
      ->create();
    $this->table("permissions")
      ->addColumn("resource", "string", ["limit" => 15,])
      ->addColumn("action", "string", ["limit" => 15,])
      ->addColumn("group", "integer")
      ->addForeignKey("group", "groups")
      ->create();
    $this->table("shops")
      ->addColumn("name", "string", ["limit" => 30,])
      ->addColumn("description", "text", ["limit" => \Phinx\Db\Adapter\MysqlAdapter::TEXT_TINY,])
      ->addIndex("name", ["unique" => true])
      ->create();
    $this->table("items")
      ->addColumn("name", "string", ["limit" => 30,])
      ->addColumn("description", "text", ["limit" => \Phinx\Db\Adapter\MysqlAdapter::TEXT_TINY,])
      ->addColumn("price", "integer", ["limit" => 3,])
      ->addColumn("shop", "integer", ["null" => true,])
      ->addColumn("type", "enum", ["values" => "item,weapon,armor,helmet,amulet,potion,material,charter,intimacy_boost", "default" => "item"])
      ->addColumn("strength", "integer", ["limit" => 3, "default" => 0,])
      ->addIndex("name", ["unique" => true])
      ->addForeignKey("shop", "shops")
      ->create();
    $this->table("item_sets")
      ->addColumn("name", "text", ["limit" => \Phinx\Db\Adapter\MysqlAdapter::TEXT_TINY,])
      ->addColumn("weapon", "integer", ["null" => true,])
      ->addColumn("armor", "integer", ["null" => true,])
      ->addColumn("helmet", "integer", ["null" => true,])
      ->addColumn("stat", "enum", ["values" => "damage,armor,hitpoints,initiative",])
      ->addColumn("bonus", "integer", ["limit" => 2,])
      ->addForeignKey("weapon", "items")
      ->addForeignKey("armor", "items")
      ->addForeignKey("helmet", "items")
      ->create();
    $this->table("meals")
      ->addColumn("name", "string", ["limit" => 15,])
      ->addColumn("message", "text")
      ->addColumn("price", "integer", ["limit" => 3,])
      ->addColumn("life", "integer", ["limit" => 2, "default" => 0,])
      ->create();
    $this->table("orders")
      ->addColumn("name", "string", ["limit" => 25,])
      ->addColumn("description", "text")
      ->addColumn("level", "integer", ["limit" => 1, "default" => 1,])
      ->addColumn("founded", "integer")
      ->addColumn("money", "integer", ["default" => 0,])
      ->create();
    $this->table("order_ranks")
      ->addColumn("name", "string", ["limit" => 10,])
      ->addColumn("adventure_bonus", "integer", ["limit" => 2, "default" => 5,])
      ->addColumn("order_fee", "integer", ["limit" => 3,])
      ->create();
    $this->table("skills")
      ->addColumn("name", "string", ["limit" => 25,])
      ->addColumn("price", "integer", ["limit" => 3,])
      ->addColumn("max_level", "integer", ["limit" => 2,])
      ->addColumn("type", "enum", ["values" => "work,combat",])
      ->addColumn("stat", "enum", ["values" => "hitpoints,damage,armor,initiative", "null" => true,])
      ->addColumn("stat_increase", "integer", ["limit" => 2, "default" => 0,])
      ->create();
    $this->table("jobs")
      ->addColumn("name", "string", ["limit" => 20,])
      ->addColumn("description", "text")
      ->addColumn("help", "text")
      ->addColumn("count", "integer", ["default" => 0,])
      ->addColumn("award", "integer")
      ->addColumn("shift", "integer")
      ->addColumn("level", "integer", ["limit" => 5, "default" => 50,])
      ->addColumn("needed_skill", "integer")
      ->addColumn("needed_skill_level", "integer", ["limit" => 1, "default" => 0,])
      ->addForeignKey("needed_skill", "skills")
      ->create();
    $this->table("job_messages")
      ->addColumn("job", "integer")
      ->addColumn("success", "boolean")
      ->addColumn("message", "text")
      ->addForeignKey("job", "jobs")
      ->create();
    $this->table("mount_types")
      ->addColumn("name", "string", ["limit" => 12,])
      ->addColumn("female_name", "string", ["limit" => 12,])
      ->addColumn("young_name", "string", ["limit" => 12,])
      ->addColumn("description", "string", ["limit" => 40,])
      ->addColumn("level", "integer", ["limit" => 5,])
      ->addColumn("damage", "integer", ["limit" => 1, "default" => 0,])
      ->addColumn("armor", "integer", ["limit" => 1, "default" => 0,])
      ->addColumn("price", "integer", ["limit" => 6,])
      ->create();
    $this->table("towns")
      ->addColumn("name", "string", ["limit" => 20,])
      ->addColumn("description", "text")
      ->addColumn("founded", "integer")
      ->addColumn("owner", "integer", ["default" => 0,])
      ->addColumn("price", "integer", ["limit" => 6, "default" => 5000,])
      ->addColumn("on_market", "boolean", ["default" => false,])
      ->create();
    $this->table("monasteries")
      ->addColumn("name", "string", ["limit" => 20,])
      ->addColumn("leader", "integer", ["default" => 0,])
      ->addColumn("town", "integer")
      ->addColumn("founded", "integer")
      ->addColumn("money", "integer", ["default" => 0,])
      ->addColumn("altair_level", "integer", ["limit" => 1, "default" => 1,])
      ->addColumn("library_level", "integer", ["limit" => 1, "default" => 0,])
      ->addColumn("hp", "integer", ["limit" => 3, "default" => 100,])
      ->addForeignKey("town", "towns")
      ->create();
    $this->table("guilds")
      ->addColumn("name", "string", ["limit" => 25,])
      ->addColumn("description", "text")
      ->addColumn("level", "integer", ["limit" => 1, "default" => 1,])
      ->addColumn("founded", "integer")
      ->addColumn("town", "integer")
      ->addColumn("money", "integer", ["default" => 0,])
      ->addColumn("skill", "integer")
      ->addIndex("name", ["unique" => true])
      ->addForeignKey("town", "towns")
      ->addForeignKey("skill", "skills")
      ->create();
    $this->table("guild_ranks")
      ->addColumn("name", "string", ["limit" => 10,])
      ->addColumn("income_bonus", "integer", ["limit" => 2, "default" => 5,])
      ->addColumn("guild_fee", "integer", ["limit" => 3,])
      ->create();
    $this->table("users")
      ->addColumn("publicname", "string", ["limit" => 25,])
      ->addColumn("password", "string", ["limit" => 60,])
      ->addColumn("email", "text", ["limit" => \Phinx\Db\Adapter\MysqlAdapter::TEXT_TINY,])
      ->addColumn("joined", "integer")
      ->addColumn("last_active", "integer", ["null" => true,])
      ->addColumn("last_prayer", "integer", ["null" => true,])
      ->addColumn("last_transfer", "integer", ["null" => true,])
      ->addColumn("group", "integer")
      ->addColumn("infomails", "boolean", ["default" => false,])
      ->addColumn("style", "string", ["limit" => 15,])
      ->addColumn("gender", "enum", ["values" => "male,female", "default" => "male"])
      ->addColumn("life", "integer", ["limit" => 2, "default" => 60,])
      ->addColumn("money", "integer")
      ->addColumn("town", "integer")
      ->addColumn("monastery", "integer", ["null" => true,])
      ->addColumn("prayers", "integer", ["default" => 0,])
      ->addColumn("guild", "integer", ["null" => true,])
      ->addColumn("guild_rank", "integer", ["null" => true,])
      ->addColumn("order", "integer", ["null" => true,])
      ->addColumn("order_rank", "integer", ["null" => true,])
      ->addForeignKey("group", "groups")
      ->addForeignKey("town", "towns")
      ->addForeignKey("monastery", "monasteries")
      ->addForeignKey("guild", "guilds")
      ->addForeignKey("guild_rank", "guild_ranks")
      ->addForeignKey("order", "orders")
      ->addForeignKey("order_rank", "order_ranks")
      ->create();
    $this->table("monastery_donations")
      ->addColumn("user", "integer")
      ->addColumn("monastery", "integer")
      ->addColumn("amount", "integer")
      ->addColumn("when", "integer")
      ->addForeignKey("user", "users")
      ->addForeignKey("monastery", "monasteries")
      ->create();
    $this->table("mounts")
      ->addColumn("name", "string", ["limit" => 25,])
      ->addColumn("gender", "enum", ["values" => "male,female,young", "default" => "young"])
      ->addColumn("type", "integer")
      ->addColumn("owner", "integer")
      ->addColumn("price", "integer", ["limit" => 6,])
      ->addColumn("on_market", "boolean", ["default" => false,])
      ->addColumn("birth", "integer")
      ->addColumn("hp", "integer", ["limit" => 3, "default" => 100,])
      ->addColumn("damage", "integer", ["limit" => 1, "default" => 0,])
      ->addColumn("armor", "integer", ["limit" => 1, "default" => 0,])
      ->addColumn("auto_feed", "boolean", ["default" => false,])
      ->addForeignKey("owner", "users")
      ->addForeignKey("type", "mount_types")
    ->create();
    $this->table("houses")
      ->addColumn("owner", "integer")
      ->addColumn("luxury_level", "integer", ["limit" => 1, "default" => 1,])
      ->addColumn("brewery_level", "integer", ["limit" => 1, "default" => 0,])
      ->addColumn("hp", "integer", ["limit" => 3, "default" => 100,])
      ->addForeignKey("owner", "users")
      ->create();
    $this->table("castles")
      ->addColumn("name", "string", ["limit" => 20,])
      ->addColumn("description", "text")
      ->addColumn("founded", "integer")
      ->addColumn("owner", "integer")
      ->addColumn("level", "integer", ["limit" => 1, "default" => 1,])
      ->addColumn("hp", "integer", ["limit" => 3, "default" => 100,])
      ->addForeignKey("owner", "users")
      ->create();
    $this->table("beer_production")
      ->addColumn("user", "integer")
      ->addColumn("house", "integer")
      ->addColumn("amount", "integer")
      ->addColumn("price", "integer")
      ->addColumn("when", "integer")
      ->addForeignKey("user", "users")
      ->addForeignKey("house", "houses")
      ->create();
    $this->table("loans")
      ->addColumn("user", "integer")
      ->addColumn("amount", "integer", ["limit" => 5,])
      ->addColumn("taken", "integer")
      ->addColumn("returned", "integer", ["null" => true,])
      ->addColumn("interest_rate", "integer", ["limit" => 2,])
      ->addForeignKey("user", "users")
      ->create();
    $this->table("deposits")
      ->addColumn("user", "integer")
      ->addColumn("amount", "integer", ["limit" => 5,])
      ->addColumn("opened", "integer")
      ->addColumn("term", "integer")
      ->addColumn("closed", "boolean", ["default" => 0,])
      ->addColumn("interest_rate", "integer", ["limit" => 2,])
      ->addForeignKey("user", "users")
      ->create();
    $this->table("articles")
      ->addColumn("title", "string", ["limit" => 30,])
      ->addColumn("text", "text")
      ->addColumn("author", "integer")
      ->addColumn("category", "enum", ["values" => "news,chronicle,poetry,short_story,essay,novella,fairy_tale,uncategorized",])
      ->addColumn("added", "integer")
      ->addColumn("allowed_comments", "boolean", ["default" => true,])
      ->addForeignKey("author", "users")
      ->create();
    $this->table("comments")
      ->addColumn("title", "string", ["limit" => 25,])
      ->addColumn("text", "text")
      ->addColumn("article", "integer")
      ->addColumn("author", "integer")
      ->addColumn("added", "integer")
      ->addForeignKey("article", "articles")
      ->addForeignKey("author", "users")
      ->create();
    $this->table("polls")
      ->addColumn("question", "string", ["limit" => 60,])
      ->addColumn("answers", "text")
      ->addColumn("author", "integer")
      ->addColumn("added", "integer")
      ->addColumn("locked", "boolean", ["default" => false,])
      ->addIndex("question", ["unique" => true])
      ->addForeignKey("author", "users")
      ->create();
    $this->table("poll_votes")
      ->addColumn("poll", "integer")
      ->addColumn("user", "integer")
      ->addColumn("answer", "integer", ["limit" => 2,])
      ->addColumn("voted", "integer")
      ->addForeignKey("poll", "polls")
      ->addForeignKey("user", "users")
      ->create();
    $this->table("elections")
      ->addColumn("candidate", "integer")
      ->addColumn("voter", "integer")
      ->addColumn("town", "integer")
      ->addColumn("when", "integer")
      ->addColumn("elected", "boolean")
      ->addForeignKey("candidate", "users")
      ->addForeignKey("voter", "users")
      ->addForeignKey("town", "towns")
      ->create();
    $this->table("election_results")
      ->addColumn("candidate", "integer")
      ->addColumn("town", "integer")
      ->addColumn("votes", "integer")
      ->addColumn("elected", "boolean")
      ->addColumn("year", "integer", ["limit" => 4,])
      ->addColumn("month", "integer", ["limit" => 2,])
      ->addForeignKey("candidate", "users")
      ->addForeignKey("town", "towns")
      ->create();
    $this->table("marriages")
      ->addColumn("user1", "integer")
      ->addColumn("user2", "integer")
      ->addColumn("status", "enum", ["values" => "proposed,accepted,declined,active,cancelled",])
      ->addColumn("divorce", "integer", ["default" => 0,])
      ->addColumn("proposed", "integer")
      ->addColumn("accepted", "integer", ["null" => true,])
      ->addColumn("term", "integer", ["null" => true,])
      ->addColumn("cancelled", "integer", ["null" => true,])
      ->addColumn("intimacy", "integer", ["limit" => 1, "default" => 0,])
      ->addForeignKey("user1", "users")
      ->addForeignKey("user2", "users")
      ->create();
    $this->table("messages")
      ->addColumn("subject", "string", ["limit" => 30,])
      ->addColumn("text", "text")
      ->addColumn("from", "integer")
      ->addColumn("to", "integer")
      ->addColumn("sent", "integer")
      ->addColumn("read", "boolean", ["default" => false,])
      ->addForeignKey("from", "users")
      ->addForeignKey("to", "users")
      ->create();
    $this->table("punishments")
      ->addColumn("user", "integer")
      ->addColumn("crime", "text")
      ->addColumn("imprisoned", "integer")
      ->addColumn("released", "integer", ["null" => true,])
      ->addColumn("number_of_shifts", "integer", ["limit" => 4,])
      ->addColumn("count", "integer", ["limit" => 4, "default" => 0,])
      ->addColumn("last_action", "integer", ["limit" => 4, "null" => true,])
      ->addForeignKey("user", "users")
      ->create();
    $this->table("user_adventures")
      ->addColumn("user", "integer")
      ->addColumn("adventure", "integer")
      ->addColumn("started", "integer")
      ->addColumn("mount", "integer")
      ->addColumn("progress", "integer", ["limit" => 2, "default" => 0,])
      ->addColumn("reward", "integer", ["limit" => 3, "default" => 0,])
      ->addColumn("loot", "integer", ["limit" => 4, "default" => 0,])
      ->addForeignKey("user", "users")
      ->addForeignKey("adventure", "adventures")
      ->addForeignKey("mount", "mounts")
      ->create();
    $this->table("user_items")
      ->addColumn("item", "integer")
      ->addColumn("user", "integer")
      ->addColumn("amount", "integer", ["limit" => 2, "default" => 1,])
      ->addColumn("worn", "boolean", ["default" => false,])
      ->addColumn("level", "integer", ["limit" => 1, "default" => 0,])
      ->addForeignKey("user", "users")
      ->addForeignKey("item", "items")
      ->create();
    $this->table("user_jobs")
      ->addColumn("user", "integer")
      ->addColumn("job", "integer")
      ->addColumn("started", "integer")
      ->addColumn("finished", "boolean", ["default" => false,])
      ->addColumn("last_action", "integer", ["null" => true,])
      ->addColumn("count", "integer", ["limit" => 4, "default" => 0,])
      ->addColumn("earned", "integer", ["limit" => 4, "default" => 0,])
      ->addColumn("extra", "integer", ["limit" => 4, "default" => 0,])
      ->addForeignKey("user", "users")
      ->addForeignKey("job", "jobs")
      ->create();
    $this->table("user_skills")
      ->addColumn("user", "integer")
      ->addColumn("skill", "integer")
      ->addColumn("level", "integer", ["limit" => 1,])
      ->addForeignKey("user", "users")
      ->addForeignKey("skill", "skills")
      ->create();
    $this->table("order_fees")
      ->addColumn("user", "integer")
      ->addColumn("order", "integer")
      ->addColumn("amount", "integer", ["default" => 0,])
      ->addIndex(["user", "order"], ["unique" => true])
      ->addForeignKey("user", "users")
      ->addForeignKey("order", "orders")
      ->create();
    $this->table("guild_fees")
      ->addColumn("user", "integer")
      ->addColumn("guild", "integer")
      ->addColumn("amount", "integer", ["default" => 0,])
      ->addIndex(["user", "guild"], ["unique" => true])
      ->addForeignKey("user", "users")
      ->addForeignKey("guild", "guilds")
      ->create();
    $this->table("chat_messages")
      ->addColumn("message", "text")
      ->addColumn("when", "integer")
      ->addColumn("user", "integer")
      ->addColumn("town", "integer", ["null" => true,])
      ->addColumn("monastery", "integer", ["null" => true,])
      ->addColumn("guild", "integer", ["null" => true,])
      ->addColumn("order", "integer", ["null" => true,])
      ->addForeignKey("user", "users")
      ->addForeignKey("town", "towns")
      ->addForeignKey("monastery", "monasteries")
      ->addForeignKey("guild", "guilds")
      ->addForeignKey("order", "orders")
      ->create();
  }
}
?>