-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `adventures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `intro` text NOT NULL,
  `epilogue` text NOT NULL,
  `level` int(5) NOT NULL DEFAULT '50',
  `reward` int(3) NOT NULL,
  `event` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event` (`event`),
  CONSTRAINT `adventures_ibfk_1` FOREIGN KEY (`event`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `adventure_npcs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `adventure` int(11) NOT NULL,
  `order` int(1) NOT NULL,
  `hitpoints` int(3) NOT NULL,
  `strength` int(2) NOT NULL,
  `armor` int(2) NOT NULL,
  `initiative` int(2) NOT NULL DEFAULT '0',
  `reward` int(3) NOT NULL,
  `encounter_text` text NOT NULL,
  `victory_text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adventure` (`adventure`),
  CONSTRAINT `adventure_npcs_ibfk_1` FOREIGN KEY (`adventure`) REFERENCES `adventures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `author` int(11) NOT NULL,
  `category` enum('news','chronicle','poetry','short_story','essay','novella','fairy_tale','uncategorized') NOT NULL,
  `added` int(11) NOT NULL,
  `allowed_comments` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `beer_production` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `house` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `when` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `house` (`house`),
  CONSTRAINT `beer_production_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `beer_production_ibfk_2` FOREIGN KEY (`house`) REFERENCES `houses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `castles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `founded` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `level` int(1) NOT NULL DEFAULT '1',
  `hp` varchar(3) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  CONSTRAINT `castles_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) NOT NULL,
  `text` text NOT NULL,
  `article` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `article` (`article`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`author`) REFERENCES `users` (`id`),
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`article`) REFERENCES `articles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `elections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate` int(11) NOT NULL,
  `voter` int(11) NOT NULL,
  `town` int(11) NOT NULL,
  `when` int(11) NOT NULL,
  `elected` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate` (`candidate`),
  KEY `voter` (`voter`),
  KEY `town` (`town`),
  CONSTRAINT `elections_ibfk_1` FOREIGN KEY (`candidate`) REFERENCES `users` (`id`),
  CONSTRAINT `elections_ibfk_2` FOREIGN KEY (`voter`) REFERENCES `users` (`id`),
  CONSTRAINT `elections_ibfk_3` FOREIGN KEY (`town`) REFERENCES `towns` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `election_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate` int(11) NOT NULL,
  `town` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  `elected` tinyint(1) NOT NULL,
  `month` int(4) NOT NULL,
  `year` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate` (`candidate`),
  KEY `town` (`town`),
  CONSTRAINT `election_results_ibfk_1` FOREIGN KEY (`candidate`) REFERENCES `users` (`id`),
  CONSTRAINT `election_results_ibfk_2` FOREIGN KEY (`town`) REFERENCES `towns` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `adventures_bonus` int(3) NOT NULL DEFAULT '0',
  `work_bonus` int(3) NOT NULL DEFAULT '0',
  `prayer_life_bonus` int(3) NOT NULL DEFAULT '0',
  `training_discount` int(3) NOT NULL DEFAULT '0',
  `repairing_discount` int(3) NOT NULL DEFAULT '0',
  `shopping_discount` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `single_name` varchar(30) NOT NULL,
  `female_name` varchar(30) NOT NULL,
  `level` int(5) NOT NULL,
  `path` enum('city','church','tower') NOT NULL,
  `max_loan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `guilds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `level` int(1) NOT NULL DEFAULT '1',
  `founded` int(11) NOT NULL,
  `town` int(11) NOT NULL,
  `money` int(11) NOT NULL DEFAULT '0',
  `skill` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `town` (`town`),
  KEY `skill` (`skill`),
  CONSTRAINT `guilds_ibfk_1` FOREIGN KEY (`town`) REFERENCES `towns` (`id`),
  CONSTRAINT `guilds_ibfk_2` FOREIGN KEY (`skill`) REFERENCES `skills` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `guild_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `income_bonus` int(2) NOT NULL DEFAULT '5',
  `guild_fee` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `houses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `luxury_level` int(1) NOT NULL DEFAULT '1',
  `brewery_level` int(1) NOT NULL DEFAULT '0',
  `hp` int(3) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  CONSTRAINT `houses_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` tinytext NOT NULL,
  `price` int(3) NOT NULL,
  `shop` int(11) DEFAULT NULL,
  `type` enum('item','weapon','armor','helmet','amulet','potion','material','charter','intimacy_boost') NOT NULL DEFAULT 'item',
  `strength` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `shop` (`shop`),
  CONSTRAINT `items_ibfk_2` FOREIGN KEY (`shop`) REFERENCES `shops` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `item_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `weapon` int(11) DEFAULT NULL,
  `armor` int(11) DEFAULT NULL,
  `helmet` int(11) DEFAULT NULL,
  `stat` enum('damage','armor','hitpoints', 'initiative') NOT NULL,
  `bonus` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weapon` (`weapon`),
  KEY `armor` (`armor`),
  KEY `helmet` (`helmet`),
  CONSTRAINT `item_sets_ibfk_1` FOREIGN KEY (`weapon`) REFERENCES `items` (`id`),
  CONSTRAINT `item_sets_ibfk_2` FOREIGN KEY (`armor`) REFERENCES `items` (`id`),
  CONSTRAINT `item_sets_ibfk_3` FOREIGN KEY (`helmet`) REFERENCES `items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `help` text NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `award` int(11) NOT NULL,
  `shift` int(11) NOT NULL,
  `level` int(5) NOT NULL DEFAULT '50',
  `needed_skill` int(11) NOT NULL,
  `needed_skill_level` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `needed_skill` (`needed_skill`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`needed_skill`) REFERENCES `skills` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `job_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job` int(11) NOT NULL,
  `success` int(1) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job` (`job`),
  CONSTRAINT `job_messages_ibfk_1` FOREIGN KEY (`job`) REFERENCES `jobs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `loans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `amount` int(5) NOT NULL,
  `taken` int(11) NOT NULL,
  `returned` int(11) DEFAULT NULL,
  `interest_rate` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `marriages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `status` enum('proposed','accepted','declined','active','cancelled') NOT NULL,
  `divorce` int(11) NOT NULL DEFAULT '0',
  `proposed` int(11) NOT NULL,
  `accepted` int(11) DEFAULT NULL,
  `term` int(11) DEFAULT NULL,
  `cancelled` int(11) DEFAULT NULL,
  `intimacy` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user1` (`user1`),
  KEY `user2` (`user2`),
  CONSTRAINT `marriages_ibfk_1` FOREIGN KEY (`user1`) REFERENCES `users` (`id`),
  CONSTRAINT `marriages_ibfk_2` FOREIGN KEY (`user2`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `meals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `message` text NOT NULL,
  `price` int(3) NOT NULL,
  `life` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `sent` int(11) NOT NULL,
  `read` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `from` (`from`),
  KEY `to` (`to`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`from`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`to`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `monasteries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `leader` int(11) NOT NULL,
  `town` int(11) NOT NULL,
  `founded` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `altair_level` int(1) NOT NULL DEFAULT '1',
  `library_level` int(1) NOT NULL DEFAULT '0',
  `hp` int(3) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `leader` (`leader`),
  KEY `town` (`town`),
  CONSTRAINT `monasteries_ibfk_1` FOREIGN KEY (`leader`) REFERENCES `users` (`id`),
  CONSTRAINT `monasteries_ibfk_2` FOREIGN KEY (`town`) REFERENCES `towns` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `monastery_donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `monastery` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `when` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `monastery` (`monastery`),
  CONSTRAINT `monastery_donations_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `monastery_donations_ibfk_2` FOREIGN KEY (`monastery`) REFERENCES `monasteries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `mounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `gender` enum('male','female','young') NOT NULL,
  `type` int(11) NOT NULL,
  `owner` int(11) DEFAULT NULL,
  `price` int(6) NOT NULL,
  `on_market` int(1) NOT NULL DEFAULT '0',
  `birth` int(11) NOT NULL,
  `hp` int(3) NOT NULL DEFAULT '100',
  `damage` int(1) NOT NULL DEFAULT '0',
  `armor` int(1) NOT NULL DEFAULT '0',
  `auto_feed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `type` (`type`),
  CONSTRAINT `mounts_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `users` (`id`),
  CONSTRAINT `mounts_ibfk_2` FOREIGN KEY (`type`) REFERENCES `mount_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `mount_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  `female_name` varchar(12) NOT NULL,
  `young_name` varchar(12) NOT NULL,
  `description` varchar(40) NOT NULL,
  `level` int(5) NOT NULL,
  `damage` int(1) NOT NULL DEFAULT '0',
  `armor` int(1) NOT NULL DEFAULT '0',
  `price` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `level` int(1) NOT NULL DEFAULT '1',
  `founded` int(11) NOT NULL,
  `money` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `order_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `adventure_bonus` int(2) NOT NULL DEFAULT '5',
  `order_fee` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource` varchar(15) NOT NULL,
  `action` varchar(15) NOT NULL,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group` (`group`),
  CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(60) NOT NULL,
  `answers` text NOT NULL,
  `author` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `locked` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `question` (`question`),
  KEY `author` (`author`),
  CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `answer` int(2) NOT NULL,
  `voted` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `poll` (`poll`),
  CONSTRAINT `poll_votes_ibfk_1` FOREIGN KEY (`poll`) REFERENCES `polls` (`id`),
  CONSTRAINT `poll_votes_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `punishments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `crime` text NOT NULL,
  `imprisoned` int(11) NOT NULL,
  `released` int(11) DEFAULT NULL,
  `number_of_shifts` int(4) NOT NULL,
  `count` int(4) NOT NULL DEFAULT '0',
  `last_action` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  CONSTRAINT `punishments_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `price` int(3) NOT NULL,
  `max_level` int(2) NOT NULL,
  `type` enum('work','combat') NOT NULL,
  `stat` enum('hitpoints','damage','armor','initiative') DEFAULT NULL,
  `stat_increase` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `towns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `founded` int(11) NOT NULL,
  `owner` int(11) NOT NULL DEFAULT '0',
  `price` int(6) NOT NULL DEFAULT '5000',
  `on_market` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  CONSTRAINT `towns_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publicname` varchar(25) NOT NULL,
  `password` varchar(60) NOT NULL,
  `email` tinytext NOT NULL,
  `joined` int(11) NOT NULL,
  `last_active` int(11) DEFAULT NULL,
  `last_prayer` int(11) DEFAULT NULL,
  `last_transfer` int(11) DEFAULT NULL,
  `group` int(11) NOT NULL DEFAULT '11',
  `infomails` int(1) NOT NULL DEFAULT '0',
  `style` varchar(15) NOT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `life` int(2) NOT NULL DEFAULT '60',
  `money` int(11) NOT NULL DEFAULT '2',
  `town` int(11) NOT NULL DEFAULT '3',
  `monastery` int(11) DEFAULT NULL,
  `prayers` int(11) NOT NULL DEFAULT '0',
  `guild` int(11) DEFAULT NULL,
  `guild_rank` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `order_rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group` (`group`),
  KEY `town` (`town`),
  KEY `monastery` (`monastery`),
  KEY `guild` (`guild`),
  KEY `guild_rank` (`guild_rank`),
  KEY `order` (`order`),
  KEY `order_rank` (`order_rank`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`town`) REFERENCES `towns` (`id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`monastery`) REFERENCES `monasteries` (`id`),
  CONSTRAINT `users_ibfk_4` FOREIGN KEY (`guild`) REFERENCES `guilds` (`id`),
  CONSTRAINT `users_ibfk_5` FOREIGN KEY (`guild_rank`) REFERENCES `guild_ranks` (`id`),
  CONSTRAINT `users_ibfk_6` FOREIGN KEY (`order`) REFERENCES `orders` (`id`),
  CONSTRAINT `users_ibfk_7` FOREIGN KEY (`order_rank`) REFERENCES `order_ranks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_adventures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `adventure` int(11) NOT NULL,
  `started` int(11) NOT NULL,
  `mount` int(11) NOT NULL,
  `progress` int(2) NOT NULL DEFAULT '0',
  `reward` int(3) NOT NULL DEFAULT '0',
  `loot` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `adventure` (`adventure`),
  KEY `mount` (`mount`),
  CONSTRAINT `user_adventures_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `user_adventures_ibfk_2` FOREIGN KEY (`adventure`) REFERENCES `adventures` (`id`),
  CONSTRAINT `user_adventures_ibfk_3` FOREIGN KEY (`mount`) REFERENCES `mounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `amount` int(2) NOT NULL DEFAULT '1',
  `worn` int(1) NOT NULL DEFAULT '0',
  `level` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `item` (`item`),
  CONSTRAINT `user_items_ibfk_1` FOREIGN KEY (`item`) REFERENCES `items` (`id`),
  CONSTRAINT `user_items_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `job` int(11) NOT NULL,
  `started` int(11) NOT NULL,
  `finished` int(1) NOT NULL DEFAULT '0',
  `last_action` int(11) DEFAULT NULL,
  `count` int(4) NOT NULL DEFAULT '0',
  `earned` int(4) NOT NULL DEFAULT '0',
  `extra` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `job` (`job`),
  KEY `user` (`user`),
  CONSTRAINT `user_jobs_ibfk_1` FOREIGN KEY (`job`) REFERENCES `jobs` (`id`),
  CONSTRAINT `user_jobs_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `skill` int(11) NOT NULL,
  `level` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `skill` (`skill`),
  KEY `user` (`user`),
  CONSTRAINT `user_skills_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `user_skills_ibfk_2` FOREIGN KEY (`skill`) REFERENCES `skills` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `amount` int(5) NOT NULL,
  `opened` int(11) NOT NULL,
  `term` int(11) NOT NULL,
  `closed` int(1) DEFAULT NULL DEFAULT '0',
  `interest_rate` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `order_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_order` (`user`,`order`),
  KEY `order` (`order`),
  CONSTRAINT `order_fees_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `order_fees_ibfk_2` FOREIGN KEY (`order`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `guild_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `guild` int(11) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_guild` (`user`,`guild`),
  KEY `guild` (`guild`),
  CONSTRAINT `guild_fees_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `guild_fees_ibfk_2` FOREIGN KEY (`guild`) REFERENCES `guilds` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `when` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `town` int(11) DEFAULT NULL,
  `monastery` int(11) DEFAULT NULL,
  `guild` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `town` (`town`),
  KEY `monastery` (`monastery`),
  KEY `guild` (`guild`),
  KEY `order` (`order`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`town`) REFERENCES `towns` (`id`),
  CONSTRAINT `chat_messages_ibfk_3` FOREIGN KEY (`monastery`) REFERENCES `monasteries` (`id`),
  CONSTRAINT `chat_messages_ibfk_4` FOREIGN KEY (`guild`) REFERENCES `guilds` (`id`),
  CONSTRAINT `chat_messages_ibfk_5` FOREIGN KEY (`order`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

-- 2016-09-25 09:05:29