-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `users` (`id`, `publicname`, `password`, `email`, `joined`, `last_active`, `last_prayer`, `last_transfer`, `group`, `infomails`, `style`, `gender`, `life`, `money`, `town`, `monastery`, `prayers`, `guild`, `guild_rank`, `order`, `order_rank`) VALUES
  (2,	'Rahym',	'$2y$10$5rhQ8Puifw9YxQ8hdK.HCOeo5AW4EhLzLrDicx1TuE3TEs.tSUmVS',	'jakub.konecny2@seznam.cz',	1435240277,	1475089811,	1446893589,	1447251643,	4,	1,	'blue-sky',	'male',	110,	14357,	1,	2,	2,	NULL,	NULL,	NULL,	NULL),
  (3,	'Jakub',	'$2y$10$ejwYft0LbhlwhLz5vA07FOs2nNZGBb4IVpxkw7i5owXgjQ1JM6iF2',	'konecnyjakub01@gmail.com',	1441219049,	1475089811,	NULL,	1447528739,	8,	1,	'blue-sky',	'male',	60,	20312,	2,	NULL,	0,	1,	4,	NULL,	NULL),
  (4,	'Světlana',	'$2y$10$0a7MCizD1w6BECZvV7p4XOyA2aGyepJQPlpzJrFwvvURcSRzGpEL.',	'svetlana@localhost.k',	1455360151,	1475089812,	1455466667,	1455466659,	5,	0,	'dark-sky',	'female',	60,	14312,	3,	NULL,	1,	NULL,	NULL,	1,	3),
  (5,	'premysl',	'$2y$10$25fvAltDnlF.TOPTj8JlK.VC2BhFmijGJNjV5HxIz1LQ9Pj0L.LQK',	'premysl@localhost.k',	1468050937,	1468050937,	NULL,	NULL,	9,	0,	'blue-sky',	'male',	60,	30,	2,	NULL,	0,	NULL,	NULL,	NULL,	NULL),
  (6,	'kazimira',	'$2y$10$Om40QnY7ELgtedNugwwziOwVjn6mPDhFBQlXr1PR/h.w4Df0xBDZi',	'kazimira@localhost.k',	1468051028,	1475089808,	NULL,	NULL,	12,	0,	'blue-sky',	'female',	60,	30,	2,	NULL,	0,	NULL,	NULL,	NULL,	NULL),
  (7,	'bozena',	'$2y$10$Z7McIrRNP6pOt9xDfgp7/uPTjXO933l9ky/Ns9XEUsiu6YN0v2G4S',	'bozena@localhost.k',	1495104741,	1495104741,	NULL,	NULL,	11,	0,	'blue-sky',	'female',	60,	30,	1,	2,	0,	NULL,	NULL,	NULL,	NULL);

INSERT INTO `castles` (`id`, `name`, `description`, `founded`, `owner`, `level`, `hp`) VALUES
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
  (2,	'Erald',	'male',	5,	1,	5000,	0,	1444840086,	100,	6,	4),
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

INSERT INTO `articles` (`id`, `title`, `text`, `author`, `category`, `added`, `allowed_comments`) VALUES
  (1,	'Nový web',	'Příprava nového webu je v plném proudu.',	1,	'news',	1434745292,	1),
  (2,	'Pokusná novinka',	'Test text test',	1,	'news',	1434750242,	1),
  (3,	'Přechod na Nextras\\Orm',	'Probíhá přechod z Nette\\Database na Nextras\\Orm.',	1,	'news',	1441225044,	1),
  (4,	'Žádné zprávy ...',	'Jak jste si možná všimli, v Nexendrii se toho poslední dobou moc nedělo (vlastně vůbec nic).',	1,	'news',	1444061049,	1),
  (5,	'Práce',	'V Nexendrii právě vznikají povolání. Povolání se vybírá na týden, po ukončení dostaneš odpovídají odměnu.',	1,	'news',	1444608626,	1),
  (6,	'Smršť změn',	'V posledním týdnu proběhlo v Nexendrii mnoho změn. Pokusím se shrnout ty nejpodstatnější.\nNejdříve byly přidány jezdecká zvířata. Dají se koupit na tržnici a je nutné o ně pravidelně pečovat a krmit je ve stájích. Prozatím nemají žádné využití, ale do budoucna se počítá, že budou využiti při cestách do vzdálených koutů nejen Nexendrie ale i okolních zemí.\nNásledně byly založeny nová města a vesnice, aby pojmuly vzrůstající populaci země. Každé město a vesnice je vlastněno šlechticem nebo vyšším duchovním, kteří s nimi mohou obchodovat. \nDále vznikly v každém městě a vesnici akademie. V nich se můžete za mírný poplatek naučit dovednosti, které vám umožní vykonávat nové práce a získat z nich větší odměnu. Každá dovednost má 5 úrovni.\nPoslední významnou změnou bylo postavení vězení, kde budou posíláni pravidla porušující obyvatelé Nexendrie. Budou tam nuceni pracovat dobu úměrnou jejich přečinu.',	1,	'news',	1445190658,	1),
  (7,	'Test',	'Lorem ipsum dota',	1,	'chronicle',	1445263644,	0),
  (8,	'Shrnutí novinek',	'Za poslední dva týdny se toho v Nexendrii událo příliš mnoho, proto se o všem zmíním jen letmo, podrobnosti můžete najít v Nápovědě. Zaprvé, rodina Žajských expandovala se svou bankou do každé vesnice a každého města v království. Dále byly vybudovány hostince a žaláře. Také přibyla (měšťanům, mnichům a šlechticům) možnost podnikat dobrodružství. Prvním je Zloděj ovcí, další brzy přibudou. Většině obyvatel země přibyla nepříjemná povinnost měsíčně odvádět daně své vrchnosti. Daní jsou nyní zatíženy příjmy z prací a dobrodružství. Naopak šlechta a vyšší duchovní mají nyní možnost ve svých městech a vesnicích tuto daň vybírat.',	1,	'news',	1446592023,	1),
  (9,	'Vzestup církví',	'Jak jistě všichni víte, v Nexendrii existuje vícero církví. V různých částech království mají různou moc a mnohdy vládnou i celým městům. Ale včera se stalo něco neočekávaného: otevřely dveře svých doposud uzavřených klášterů všem, kteří se chtějí připojit. Život v klášteře přináší často přísná pravidla a mnoho odříkání.',	1,	'news',	1446769232,	1),
  (10,	'Cechy',	'V městech a vesnicích Nexendrie začínají vznikat cechy. Ty přijímají do svých řad sedláky a měšťany z 1 města či vesnice a zvyšují jejich příjmy z práce. Avšak každý člen musí každý měsíc platit členský poplatek, jehož výše závisí na postavení v cechů. Podrobnosti můžete najít v nápovědě.',	1,	'news',	1453732535,	1),
  (11,	'Řády',	'V Nexendrii začínají vznikat řády. Řád je prestižní organizace, ve které se sdružují šlechtici a která zvyšuje jeho členům příjem z dobrodružství. Každý člen musí měsíčně odvádět členský příspěvek podle své hodnosti, z vybraných měsíc může být řád vylepšen, což dále zvyšuje příjmy členů z dobrodružství. Založit řád mohou jen příslušníci vyšší šlechty (páni, vévodové a členové Korunní rady) a stojí je to 1200 grošů.',	1,	'news',	1465133303,	1),
  (12,	'Manželství',	'V Nexendrii je nyní možné uzavřít manželství. Prozatím z něj neplynou žádné výhody, ale brzy jistě nějaké přibudou. Další podrobnosti, včetně vysvětlení jak uzavřít manželství, naleznete v nápovědě.',	1,	'news',	1466943412,	1),
  (13,	'Změny v přihlašování',	'Ve vývojové verzi Nexendrie zmizelo uživatelské jméno a používá se místo něj při přihlašování e-mailová adresa. Jméno, které se zobrazuje ostatním, zůstává, ale nyní se zadává přímo při registraci (dříve bylo stejné jako uživatelské jméno, s tím že šlo později změnit).\nDůvodem je, že 2 jména byla matoucí (a jen 1 z nich šlo měnit) a navíc e-mailová adresa nebyla jinak využitá.',	1,	'news',	1551718915,	1),
  (14,	'Přejmenován vévodský titul',	'Z nařízení Jejího Veličenstva se s okamžitou platností mění titul vévoda (nejvyšší běžný šlechtický titul) na markrabě.\nOdůvodnění: vévodský titul je normálně vyšší než knížecí (tento titul mají členové Korunní rady a také 2 knížecí tituly má i panovník Nexendrie), vévoda může být i suverénní panovník (stejně jako kníže a na rozdíl od markraběte) a také vévodský titul je od časů císařství všeobecně nenáviděn.\nDodatek: všem dotčeným osobám se na jejich profilech už zobrazuje nový titul, nápověda v beta verzi bude aktualizována při nasazení příští verze (podle plánu 1. června), v alfa verzi se tak již stalo.',	1,	'news',	1551719606,	1),
  (15,	'Dostupnější jezdecká zvířata',	'U některých typů jezdeckých zvířat byl právě snížen požadovaný titul pro jejich vlastnictví. Konkrétně osli jsou nyní dostupní pro vsechny měšťany, nejen konšely (a kněží a šlechtice). A draka si nyní může pořídit každý vyšší šlechtic, nejen markrabata a členové Korunní rady.',	1,	'news',	1552308070,	1),
  (16,	'Oslabení církví',	'Po několika letech snažení se královně podařilo prosadit zákon, který zakazuje velekněžím vlastnit města a vesnice. U královského dvora převládal názor, že církev by měla vlastnit pouze kláštery a nezasahovat do řízení měst a vesnic. Nyní je mohou na trhu koupit pouze šlechtici (vyšší i nižší) a Korunní rada se zavázala, že je přestane udělovat velekněžím.\nTato změna je prozatím pouze na alfa verzi, na betě se objeví 1. června.',	1,	'news',	1558267593,	1);

INSERT INTO `comments` (`id`, `title`, `text`, `article`, `author`, `added`) VALUES
  (1,	'Test',	'komentář',	1,	1,	1435085477),
  (2,	'Test',	'test test test test test',	2,	1,	1435250522),
  (3,	'Test 2',	'text',	2,	1,	1441223636),
  (4,	'Hotovo',	'Přechod již byl úspěšně dokončen.',	3,	1,	1441372474),
  (5,	'Test',	'text',	6,	1,	1445197436);

INSERT INTO `monasteries` (`id`, `name`, `leader`, `town`, `founded`, `money`, `altair_level`, `library_level`, `hp`) VALUES
  (2,	'Dům Jaly',	2,	1,	1447251495,	0,	6, 0,	100);

INSERT INTO `events` (`id`, `name`, `description`, `start`, `end`, `adventures_bonus`, `work_bonus`, `prayer_life_bonus`, `training_discount`, `repairing_discount`, `shopping_discount`) VALUES
  (1,	'Oslavy založení',	'd',	1467756000,	1468360740,	50,	50,	50,	50,	50,	50);

INSERT INTO `punishments` (`id`, `user`, `crime`, `imprisoned`, `released`, `number_of_shifts`, `count`, `last_action`) VALUES
  (1,	2,	'zlobil',	1445172553,	1445179141,	5,	5,	1445178236);

INSERT INTO `user_skills` (`id`, `user`, `skill`, `level`) VALUES
  (1,	1,	3,	5),
  (2,	1,	6,	5),
  (3,	2,	1,	5),
  (4,	2,	6,	5),
  (5,	3,	1,	5),
  (6,	3,	6,	5),
  (7,	4,	5,	5),
  (8,	1,	7,	10);

INSERT INTO `user_items` (`id`, `item`, `user`, `amount`, `worn`, `level`) VALUES
  (1,	9,	1,	1,	1,	3),
  (2,	13,	1,	1,	1,	3),
  (3,	14,	1,	3,	0,	0),
  (4,	13,	3,	1,	1,	3),
  (5,	18,	1,	1,	1,	1),
  (6,	13,	2,	1,	1,	0),
  (7,	17,	2,	1,	0,	0),
  (8,	13,	4,	1,	0,	0),
  (9,	24,	4,	1,	0,	0),
  (10,	2,	4,	1,	0,	0),
  (11,	22,	2,	1,	1,	0),
  (12,	24,	3,	1,	1,	3),
  (13,	18,	4,	1,	0,	0),
  (14,	18,	2,	1,	1,	0),
  (15,	18,	3,	1,	1,	1),
  (16,	35,	4,	1,	1,	2),
  (17,	38,	4,	1,	1,	2),
  (18,	41,	4,	1,	1,	2),
  (19,	44,	1,	4,	0,	0),
  (20,	25,	3,	1,	0,	0),
  (21,	1,	3,	1,	0,	0),
  (22,	25,	1,	1,	0,	0),
  (23, 3, 1, 1, 0, 0);

INSERT INTO `beer_production` (`id`, `user`, `house`, `amount`, `price`, `when`) VALUES
  (1,	3,	1,	1,	30,	1449771650),
  (2,	3,	1,	1,	30,	1450376603),
  (3,	3,	1,	1,	30,	1450706268),
  (4,	3,	1,	1,	30,	1452105091),
  (5,	3,	1,	1,	30,	1452719105),
  (6,	3,	1,	1,	30,	1453401603),
  (7,	3,	1,	1,	30,	1454834066),
  (8,	3,	1,	1,	30,	1455448359),
  (9,	3,	1,	1,	30,	1456064093),
  (10,	3,	1,	1,	30,	1457266437),
  (11,	3,	1,	1,	30,	1457883895),
  (12,	3,	1,	1,	30,	1458489138),
  (13,	3,	1,	1,	30,	1459097957),
  (14,	3,	1,	1,	30,	1460798232),
  (15,	3,	1,	1,	30,	1461601500),
  (16,	3,	1,	1,	30,	1462464687),
  (17,	3,	1,	1,	30,	1463835106),
  (18,	3,	1,	1,	30,	1464511812),
  (19,	3,	1,	1,	30,	1465117203),
  (20,	3,	1,	1,	30,	1465731299),
  (21,	3,	1,	1,	30,	1466337743),
  (22,	3,	1,	2,	30,	1466943222),
  (23,	3,	1,	2,	30,	1467552000),
  (24,	3,	1,	5,	30,	1468175438),
  (25,	3,	1,	5,	30,	1469559471),
  (26,	3,	1,	5,	30,	1470470909),
  (27,	3,	1,	5,	30,	1471633090),
  (28,	3,	1,	5,	30,	1472284608),
  (29,	3,	1,	5,	30,	1473220001),
  (30,	3,	1,	5,	30,	1473862866),
  (31,	3,	1,	5,	30,	1474467761);

INSERT INTO `marriages` (`id`, `user1`, `user2`, `status`, `divorce`, `proposed`, `accepted`, `term`, `cancelled`, `intimacy`) VALUES
  (1,	4,	1,	'active',	0,	1466241338,	1466245558,	1467450938,	NULL,	5),
  (2,	3,	6,	'accepted',	0,	1475264924,	1475264945,	1792007340,	NULL,	0);


INSERT INTO `chat_messages` (`id`, `message`, `when`, `user`, `town`, `monastery`, `guild`, `order`) VALUES
  (1,	'Vítejte v cechu',	1521573723,	3,	NULL,	NULL,	1,	NULL);

-- 2016-09-25 09:13:21