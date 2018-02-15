SET standard_conforming_strings=off;
SET escape_string_warning=off;
SET CONSTRAINTS ALL DEFERRED;

CREATE TABLE "adventure_npcs" (
    "id" SERIAL NOT NULL,
    "name" varchar(30) NOT NULL,
    "adventure" integer NOT NULL,
    "order" integer NOT NULL,
    "hitpoints" integer NOT NULL,
    "strength" integer NOT NULL,
    "armor" integer NOT NULL,
    "reward" integer NOT NULL,
    "encounter_text" text NOT NULL,
    "victory_text" text NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "adventures" (
    "id" SERIAL NOT NULL,
    "name" varchar(40) NOT NULL,
    "description" text NOT NULL,
    "intro" text NOT NULL,
    "epilogue" text NOT NULL,
    "level" integer NOT NULL DEFAULT 50,
    "reward" integer NOT NULL,
    "event" integer DEFAULT NULL,
    PRIMARY KEY ("id")
);

CREATE TYPE articles_category AS ENUM ('news','chronicle','poetry','short_story','essay','novella','fairy_tale','uncategorized'); 
CREATE TABLE "articles" (
    "id" SERIAL NOT NULL,
    "title" varchar(60) NOT NULL,
    "text" text NOT NULL,
    "author" integer NOT NULL,
    "category" articles_category NOT NULL,
    "added" integer NOT NULL,
    "allowed_comments" boolean NOT NULL DEFAULT TRUE,
    PRIMARY KEY ("id")
);

CREATE TABLE "beer_production" (
    "id" SERIAL NOT NULL,
    "user" integer NOT NULL,
    "house" integer NOT NULL,
    "amount" integer NOT NULL,
    "price" integer NOT NULL,
    "when" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "castles" (
    "id" SERIAL NOT NULL,
    "name" varchar(40) NOT NULL,
    "description" text NOT NULL,
    "founded" integer NOT NULL,
    "owner" integer NOT NULL,
    "level" integer NOT NULL DEFAULT 1,
    "hp" varchar(6) NOT NULL DEFAULT 100,
    PRIMARY KEY ("id")
);

CREATE TABLE "comments" (
    "id" SERIAL NOT NULL,
    "title" varchar(50) NOT NULL,
    "text" text NOT NULL,
    "article" integer NOT NULL,
    "author" integer NOT NULL,
    "added" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "deposits" (
    "id" int NOT NULL,
    "user" int NOT NULL,
    "amount" int NOT NULL,
    "opened" int NOT NULL,
    "term" int NOT NULL,
    "closed" boolean DEFAULT FALSE,
    "interest_rate" int NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "election_results" (
    "id" SERIAL NOT NULL,
    "candidate" integer NOT NULL,
    "town" integer NOT NULL,
    "votes" integer NOT NULL,
    "elected" int4 NOT NULL,
    "month" integer NOT NULL,
    "year" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "elections" (
    "id" SERIAL NOT NULL,
    "candidate" integer NOT NULL,
    "voter" integer NOT NULL,
    "town" integer NOT NULL,
    "when" integer NOT NULL,
    "elected" boolean NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "events" (
    "id" SERIAL NOT NULL,
    "name" varchar(40) NOT NULL,
    "description" text NOT NULL,
    "start" integer NOT NULL,
    "end" integer NOT NULL,
    "adventures_bonus" integer NOT NULL DEFAULT 0,
    "work_bonus" integer NOT NULL DEFAULT 0,
    "prayer_life_bonus" integer NOT NULL DEFAULT 0,
    "training_discount" integer NOT NULL DEFAULT 0,
    "repairing_discount" integer NOT NULL DEFAULT 0,
    "shopping_discount" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TYPE groups_path AS ENUM ('city','church','tower'); 
CREATE TABLE "groups" (
    "id" SERIAL NOT NULL,
    "name" varchar(60) NOT NULL,
    "single_name" varchar(60) NOT NULL,
    "female_name" varchar(60) NOT NULL,
    "level" integer NOT NULL,
    "path" groups_path NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "guild_fees" (
  "id" SERIAL NOT NULL,
  "user" integer NOT NULL,
  "guild" integer NOT NULL,
  "amount" integer NOT NULL DEFAULT 0,
  PRIMARY KEY ("id"),
  UNIQUE ("user", "guild")
);

CREATE TABLE "guild_ranks" (
    "id" SERIAL NOT NULL,
    "name" varchar(20) NOT NULL,
    "income_bonus" integer NOT NULL DEFAULT 5,
    "guild_fee" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "guilds" (
    "id" SERIAL NOT NULL,
    "name" varchar(50) NOT NULL,
    "description" text NOT NULL,
    "level" integer NOT NULL DEFAULT 1,
    "founded" integer NOT NULL,
    "town" integer NOT NULL,
    "money" integer NOT NULL DEFAULT 0,
    "skill" integer NOT NULL,
    PRIMARY KEY ("id"),
    UNIQUE ("name")
);

CREATE TABLE "houses" (
    "id" SERIAL NOT NULL,
    "owner" integer NOT NULL,
    "luxury_level" integer NOT NULL DEFAULT 1,
    "brewery_level" integer NOT NULL DEFAULT 0,
    "hp" integer NOT NULL DEFAULT 100,
    PRIMARY KEY ("id")
);

CREATE TYPE item_sets_stat AS ENUM ('damage','armor','hitpoints'); 
CREATE TABLE "item_sets" (
    "id" SERIAL NOT NULL,
    "name" text NOT NULL,
    "weapon" integer DEFAULT NULL,
    "armor" integer DEFAULT NULL,
    "helmet" integer DEFAULT NULL,
    "stat" item_sets_stat NOT NULL,
    "bonus" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TYPE items_type AS ENUM ('item','weapon','armor','helmet','potion','material','charter','intimacy_boost');
CREATE TABLE "items" (
    "id" SERIAL NOT NULL,
    "name" varchar(60) NOT NULL,
    "description" text NOT NULL,
    "price" integer NOT NULL,
    "shop" integer DEFAULT NULL,
    "type" items_type NOT NULL DEFAULT 'item',
    "strength" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id"),
    UNIQUE ("name")
);

CREATE TABLE "job_messages" (
    "id" SERIAL NOT NULL,
    "job" integer NOT NULL,
    "success" boolean NOT NULL,
    "message" text NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "jobs" (
    "id" SERIAL NOT NULL,
    "name" varchar(40) NOT NULL,
    "description" text NOT NULL,
    "help" text NOT NULL,
    "count" integer NOT NULL DEFAULT 0,
    "award" integer NOT NULL,
    "shift" integer NOT NULL,
    "level" integer NOT NULL DEFAULT 5,
    "needed_skill" integer NOT NULL,
    "needed_skill_level" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "loans" (
    "id" SERIAL NOT NULL,
    "user" integer NOT NULL,
    "amount" integer NOT NULL,
    "taken" integer NOT NULL,
    "returned" integer DEFAULT NULL,
    "interest_rate" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TYPE marriages_status AS ENUM ('proposed','accepted','declined','active','cancelled'); 
CREATE TABLE "marriages" (
    "id" SERIAL NOT NULL,
    "user1" integer NOT NULL,
    "user2" integer NOT NULL,
    "status" marriages_status NOT NULL,
    "divorce" integer NOT NULL DEFAULT 0,
    "proposed" integer NOT NULL,
    "accepted" integer DEFAULT NULL,
    "term" integer DEFAULT NULL,
    "cancelled" integer DEFAULT NULL,
    "intimacy" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "meals" (
    "id" SERIAL NOT NULL,
    "name" varchar(30) NOT NULL,
    "message" text NOT NULL,
    "price" integer NOT NULL,
    "life" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "messages" (
    "id" SERIAL NOT NULL,
    "subject" varchar(60) NOT NULL,
    "text" text NOT NULL,
    "from" integer NOT NULL,
    "to" integer NOT NULL,
    "sent" integer NOT NULL,
    "read" boolean NOT NULL DEFAULT FALSE,
    PRIMARY KEY ("id")
);

CREATE TABLE "monasteries" (
    "id" SERIAL NOT NULL,
    "name" varchar(40) NOT NULL,
    "leader" integer NOT NULL,
    "town" integer NOT NULL,
    "founded" integer NOT NULL,
    "money" integer NOT NULL,
    "level" integer NOT NULL DEFAULT 1,
    "hp" integer NOT NULL DEFAULT 100,
    PRIMARY KEY ("id")
);

CREATE TABLE "monastery_donations" (
    "id" SERIAL NOT NULL,
    "user" integer NOT NULL,
    "monastery" integer NOT NULL,
    "amount" integer NOT NULL,
    "when" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "mount_types" (
    "id" SERIAL NOT NULL,
    "name" varchar(24) NOT NULL,
    "female_name" varchar(24) NOT NULL,
    "young_name" varchar(24) NOT NULL,
    "description" varchar(80) NOT NULL,
    "level" integer NOT NULL,
    "damage" integer NOT NULL DEFAULT 0,
    "armor" integer NOT NULL DEFAULT 0,
    "price" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TYPE mounts_gender AS ENUM ('male','female','young'); 
CREATE TABLE "mounts" (
    "id" SERIAL NOT NULL,
    "name" varchar(50) NOT NULL,
    "gender" mounts_gender NOT NULL,
    "type" integer NOT NULL,
    "owner" integer DEFAULT NULL,
    "price" integer NOT NULL,
    "on_market" boolean NOT NULL DEFAULT FALSE,
    "birth" integer NOT NULL,
    "hp" integer NOT NULL DEFAULT 100,
    "damage" integer NOT NULL DEFAULT 0,
    "armor" integer NOT NULL DEFAULT 0,
    "auto_feed" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "order_fees" (
    "id" SERIAL  NOT NULL,
    "user" INTEGER NOT NULL,
    "order" INTEGER NOT NULL,
    "amount" INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY ("id"),
    UNIQUE ("user", "order")
);

CREATE TABLE "order_ranks" (
    "id" SERIAL NOT NULL,
    "name" varchar(20) NOT NULL,
    "adventure_bonus" integer NOT NULL DEFAULT 5,
    "order_fee" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "orders" (
    "id" SERIAL NOT NULL,
    "name" varchar(50) NOT NULL,
    "description" text NOT NULL,
    "level" integer NOT NULL DEFAULT 1,
    "founded" integer NOT NULL,
    "money" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "permissions" (
    "id" SERIAL NOT NULL,
    "resource" varchar(30) NOT NULL,
    "action" varchar(30) NOT NULL,
    "group" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "poll_votes" (
    "id" SERIAL NOT NULL,
    "poll" integer NOT NULL,
    "user" integer NOT NULL,
    "answer" integer NOT NULL,
    "voted" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "polls" (
    "id" SERIAL NOT NULL,
    "question" varchar(120) NOT NULL,
    "answers" text NOT NULL,
    "author" integer NOT NULL,
    "added" integer NOT NULL,
    "locked" boolean NOT NULL DEFAULT FALSE,
    PRIMARY KEY ("id"),
    UNIQUE ("question")
);

CREATE TABLE "punishments" (
    "id" SERIAL NOT NULL,
    "user" integer NOT NULL,
    "crime" text NOT NULL,
    "imprisoned" integer NOT NULL,
    "released" integer DEFAULT NULL,
    "number_of_shifts" integer NOT NULL,
    "count" integer NOT NULL DEFAULT 0,
    "last_action" integer DEFAULT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "shops" (
    "id" SERIAL NOT NULL,
    "name" varchar(60) NOT NULL,
    "description" text NOT NULL,
    PRIMARY KEY ("id"),
    UNIQUE ("name")
);

CREATE TYPE skills_type AS ENUM ('work','combat'); 
CREATE TYPE skills_stat AS ENUM ('hitpoints','damage','armor'); 
CREATE TABLE "skills" (
    "id" SERIAL NOT NULL,
    "name" varchar(50) NOT NULL,
    "price" integer NOT NULL,
    "max_level" integer NOT NULL,
    "type" skills_type NOT NULL,
    "stat" skills_stat DEFAULT NULL,
    "stat_increase" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "towns" (
    "id" SERIAL NOT NULL,
    "name" varchar(40) NOT NULL,
    "description" text NOT NULL,
    "founded" integer NOT NULL,
    "owner" integer NOT NULL DEFAULT 0,
    "price" integer NOT NULL DEFAULT 5000,
    "on_market" boolean NOT NULL DEFAULT FALSE,
    PRIMARY KEY ("id")
);

CREATE TABLE "user_adventures" (
    "id" SERIAL NOT NULL,
    "user" integer NOT NULL,
    "adventure" integer NOT NULL,
    "started" integer NOT NULL,
    "mount" integer NOT NULL,
    "progress" integer NOT NULL DEFAULT 0,
    "reward" integer NOT NULL DEFAULT 0,
    "loot" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "user_items" (
    "id" SERIAL NOT NULL,
    "item" integer NOT NULL,
    "user" integer NOT NULL,
    "amount" integer NOT NULL DEFAULT 1,
    "worn" boolean NOT NULL DEFAULT FALSE,
    "level" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "user_jobs" (
    "id" SERIAL NOT NULL,
    "user" integer NOT NULL,
    "job" integer NOT NULL,
    "started" integer NOT NULL,
    "finished" boolean NOT NULL DEFAULT FALSE,
    "last_action" integer DEFAULT NULL,
    "count" integer NOT NULL DEFAULT 0,
    "earned" integer NOT NULL DEFAULT 0,
    "extra" integer NOT NULL DEFAULT 0,
    PRIMARY KEY ("id")
);

CREATE TABLE "user_skills" (
    "id" SERIAL NOT NULL,
    "user" integer NOT NULL,
    "skill" integer NOT NULL,
    "level" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TYPE users_gender AS ENUM ('male','female');
CREATE TABLE "users" (
    "id" SERIAL NOT NULL,
    "username" varchar(50) NOT NULL,
    "publicname" varchar(50) NOT NULL,
    "password" varchar(120) NOT NULL,
    "email" text NOT NULL,
    "joined" integer NOT NULL,
    "last_active" integer DEFAULT NULL,
    "last_prayer" integer DEFAULT NULL,
    "last_transfer" integer DEFAULT NULL,
    "group" integer NOT NULL DEFAULT 11,
    "infomails" boolean NOT NULL DEFAULT FALSE,
    "style" varchar(30) NOT NULL DEFAULT 'default',
    "gender" users_gender NOT NULL DEFAULT 'male',
    "banned" boolean NOT NULL DEFAULT FALSE,
    "life" integer NOT NULL DEFAULT 60,
    "max_life" integer NOT NULL DEFAULT 60,
    "money" integer NOT NULL DEFAULT 2,
    "town" integer NOT NULL DEFAULT 3,
    "monastery" integer DEFAULT NULL,
    "castle" integer DEFAULT NULL,
    "house" integer DEFAULT NULL,
    "prayers" integer NOT NULL DEFAULT 0,
    "guild" integer DEFAULT NULL,
    "guild_rank" integer DEFAULT NULL,
    "order" integer DEFAULT NULL,
    "order_rank" integer DEFAULT NULL,
    PRIMARY KEY ("id"),
    UNIQUE ("username")
);
