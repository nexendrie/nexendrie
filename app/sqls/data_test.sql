-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `castles` (`id`, `name`, `description`, `founded`, `owner`, `level`, `hp`) VALUES
  (1,	'Dračí hrad',	'Dračí hrad je již od nepaměti sídlem panovníků Nexendrie.',	1429779664,	0,	5,	'100'),
  (2,	'Falver',	'.',	1447420077,	1,	5,	'100'),
  (3,	'Erdvor',	'.',	1466869822,	4,	3,	'100');

INSERT INTO `guilds` (`id`, `name`, `description`, `level`, `founded`, `town`, `money`, `skill`) VALUES
  (1,	'Cech kupců z Myhru',	'.',	2,	1453484840,	2,	300,	6);

INSERT INTO `houses` (`id`, `owner`, `luxury_level`, `brewery_level`, `hp`) VALUES
  (1,	3,	5,	5,	100);

INSERT INTO `messages` (`id`, `subject`, `text`, `from`, `to`, `sent`, `read`) VALUES
  (1,	'Test',	'Test message.',	2,	1,	1434731668,	1),
  (2,	'Test',	'Test message.',	1,	2,	1434731668,	1),
  (3,	'Zpráva',	'text text text',	1,	2,	1434904922,	1),
  (4,	'Orm',	'Lorem ipsum dota',	1,	3,	1441278929,	0),
  (5,	'Test',	'Just a test.',	1,	3,	1441307001,	0),
  (6,	'Test',	'tttttest',	1,	3,	1444060591,	0),
  (7,	'Povýšení',	'Již nějakou dobu jsi řádným občanem Nexendrie a proto jsi byl povýšen na Měšťana.',	1,	3,	1447529598,	0),
  (8,	'Dárek',	'Dostal jsi 1000 grošů a  Právo na založení města.',	1,	1,	1447595907,	1),
  (9,	'Povýšení',	'Byl jsi povýšen na měšťana.',	1,	3,	1448473816,	1),
  (10,	'Povýšení',	'Byl jsi povýšen na měšťana.',	1,	5,	1468075669,	0);

INSERT INTO `mounts` (`id`, `name`, `gender`, `type`, `owner`, `price`, `on_market`, `birth`, `hp`, `damage`, `armor`) VALUES
  (1,	'Mel',	'male',	1,	0,	50,	1,	1444838883,	100,	0,	0),
  (2,	'Erald',	'male',	5,	1,	5000,	0,	1444840086,	100,	7,	5),
  (3,	'Larna',	'female',	1,	0,	50,	1,	1444859395,	100,	0,	0),
  (4,	'Mil',	'male',	1,	0,	50,	1,	1444859656,	100,	0,	0),
  (5,	'Zimma',	'female',	1,	3,	50,	1,	1444859791,	100,	0,	0),
  (6,	'Ivlis',	'male',	4,	2,	1800,	0,	1446756290,	100,	3,	5),
  (7,	'Buris',	'male',	2,	0,	127,	1,	1447342669,	100,	0,	1),
  (8,	'Bila',	'female',	2,	3,	127,	0,	1447343032,	100,	1,	3),
  (9,	'Durhil',	'young',	5,	0,	5000,	1,	1447513936,	100,	3,	2),
  (10,	'Lana',	'female',	4,	4,	400,	0,	1455361161,	100,	3,	5),
  (11,	'Ihb An',	'male',	3,	0,	300,	1,	1465734704,	100,	1,	0),
  (12,	'Valdan',	'male',	2,	0,	100,	1,	1465735652,	100,	0,	1);

INSERT INTO `orders` (`id`, `name`, `description`, `level`, `founded`, `money`) VALUES
  (1,	'Řád dračích jezdců',	'.',	2,	1465120352,	400);

INSERT INTO `polls` (`id`, `question`, `answers`, `author`, `added`, `locked`) VALUES
  (1,	'Otázka',	'Možnost 1\nMožnost 2\nMožnost 3\nMožnost 4',	1,	1435673273,	0),
  (2,	'Tvé oblíbené ORM',	'Doctrine\nLeanMapper\nNextras\\Orm',	1,	1441236118,	0),
  (3,	'Tvůj oblíbený framework',	'Nette\nSymfony\nLaravel\nZend\nCodeIgniter',	1,	1444060844,	0);

-- 2016-09-25 09:13:21
