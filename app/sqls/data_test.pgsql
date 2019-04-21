SET standard_conforming_strings=off;
SET escape_string_warning=off;
SET CONSTRAINTS ALL DEFERRED;

INSERT INTO articles ("title", "text", "author", "category", "added", "allowed_comments") VALUES
  ('Nový web','Příprava nového webu je v plném proudu.',1,'news',1434745292,TRUE),
  ('Pokusná novinka','Test text test',1,'news',1434750242,TRUE),
  ('Přechod na Nextras\\Orm','Probíhá přechod z Nette\\Database na Nextras\\Orm.',1,'news',1441225044,TRUE),
  ('Žádné zprávy ...','Jak jste si možná všimli, v Nexendrii se toho poslední dobou moc nedělo (vlastně vůbec nic).',1,'news',1444061049,TRUE),
  ('Práce','V Nexendrii právě vznikají povolání. Povolání se vybírá na týden, po ukončení dostaneš odpovídají odměnu.',1,'news',1444608626,TRUE),
  ('Smršť změn','V posledním týdnu proběhlo v Nexendrii mnoho změn. Pokusím se shrnout ty nejpodstatnější.\nNejdříve byly přidány jezdecká zvířata. Dají se koupit na tržnici a je nutné o ně pravidelně pečovat a krmit je ve stájích. Prozatím nemají žádné využití, ale do budoucna se počítá, že budou využiti při cestách do vzdálených koutů nejen Nexendrie ale i okolních zemí.\nNásledně byly založeny nová města a vesnice, aby pojmuly vzrůstající populaci země. Každé město a vesnice je vlastněno šlechticem nebo vyšším duchovním, kteří s nimi mohou obchodovat. \nDále vznikly v každém městě a vesnici akademie. V nich se můžete za mírný poplatek naučit dovednosti, které vám umožní vykonávat nové práce a získat z nich větší odměnu. Každá dovednost má 5 úrovni.\nPoslední významnou změnou bylo postavení vězení, kde budou posíláni pravidla porušující obyvatelé Nexendrie. Budou tam nuceni pracovat dobu úměrnou jejich přečinu.',1,'news',1445190658,TRUE),
  ('Test','Lorem ipsum dota',1,'chronicle',1445263644,FALSE),
  ('Shrnutí novinek','Za poslední dva týdny se toho v Nexendrii událo příliš mnoho, proto se o všem zmíním jen letmo, podrobnosti můžete najít v Nápovědě. Zaprvé, rodina Žajských expandovala se svou bankou do každé vesnice a každého města v království. Dále byly vybudovány hostince a žaláře. Také přibyla (měšťanům, mnichům a šlechticům) možnost podnikat dobrodružství. Prvním je Zloděj ovcí, další brzy přibudou. Většině obyvatel země přibyla nepříjemná povinnost měsíčně odvádět daně své vrchnosti. Daní jsou nyní zatíženy příjmy z prací a dobrodružství. Naopak šlechta a vyšší duchovní mají nyní možnost ve svých městech a vesnicích tuto daň vybírat.',1,'news',1446592023,TRUE),
  ('Vzestup církví','Jak jistě všichni víte, v Nexendrii existuje vícero církví. V různých částech království mají různou moc a mnohdy vládnou i celým městům. Ale včera se stalo něco neočekávaného: otevřely dveře svých doposud uzavřených klášterů všem, kteří se chtějí připojit. Život v klášteře přináší často přísná pravidla a mnoho odříkání.',1,'news',1446769232,TRUE),
  ('Cechy','V městech a vesnicích Nexendrie začínají vznikat cechy. Ty přijímají do svých řad sedláky a měšťany z 1 města či vesnice a zvyšují jejich příjmy z práce. Avšak každý člen musí každý měsíc platit členský poplatek, jehož výše závisí na postavení v cechů. Podrobnosti můžete najít v nápovědě.',1,'news',1453732535,TRUE),
  ('Řády','V Nexendrii začínají vznikat řády. Řád je prestižní organizace, ve které se sdružují šlechtici a která zvyšuje jeho členům příjem z dobrodružství. Každý člen musí měsíčně odvádět členský příspěvek podle své hodnosti, z vybraných měsíc může být řád vylepšen, což dále zvyšuje příjmy členů z dobrodružství. Založit řád mohou jen příslušníci vyšší šlechty (páni, vévodové a členové Korunní rady) a stojí je to 1200 grošů.',1,'news',1465133303,TRUE),
  ('Manželství','V Nexendrii je nyní možné uzavřít manželství. Prozatím z něj neplynou žádné výhody, ale brzy jistě nějaké přibudou. Další podrobnosti, včetně vysvětlení jak uzavřít manželství, naleznete v nápovědě.',1,'news',1466943412,TRUE),
  ('Změny v přihlašování',	'Ve vývojové verzi Nexendrie zmizelo uživatelské jméno a používá se místo něj při přihlašování e-mailová adresa. Jméno, které se zobrazuje ostatním, zůstává, ale nyní se zadává přímo při registraci (dříve bylo stejné jako uživatelské jméno, s tím že šlo později změnit).\nDůvodem je, že 2 jména byla matoucí (a jen 1 z nich šlo měnit) a navíc e-mailová adresa nebyla jinak využitá.',	1,	'news',	1551718915,	TRUE),
  ('Přejmenován vévodský titul',	'Z nařízení Jejího Veličenstva se s okamžitou platností mění titul vévoda (nejvyšší běžný šlechtický titul) na markrabě.\nOdůvodnění: vévodský titul je normálně vyšší než knížecí (tento titul mají členové Korunní rady a také 2 knížecí tituly má i panovník Nexendrie), vévoda může být i suverénní panovník (stejně jako kníže a na rozdíl od markraběte) a také vévodský titul je od časů císařství všeobecně nenáviděn.\nDodatek: všem dotčeným osobám se na jejich profilech už zobrazuje nový titul, nápověda v beta verzi bude aktualizována při nasazení příští verze (podle plánu 1. června), v alfa verzi se tak již stalo.',	1,	'news',	1551719606,	TRUE),
  ('Dostupnější jezdecká zvířata',	'U některých typů jezdeckých zvířat byl právě snížen požadovaný titul pro jejich vlastnictví. Konkrétně osli jsou nyní dostupní pro vsechny měšťany, nejen konšely (a kněží a šlechtice). A draka si nyní může pořídit každý vyšší šlechtic, nejen markrabata a členové Korunní rady.',	1,	'news',	1552308070,	TRUE);

INSERT INTO beer_production ("user", "house", "amount", "price", "when") VALUES
  (3,1,1,30,1449771650),
  (3,1,1,30,1450376603),
  (3,1,1,30,1450706268),
  (3,1,1,30,1452105091),
  (3,1,1,30,1452719105),
  (3,1,1,30,1453401603),
  (3,1,1,30,1454834066),
  (3,1,1,30,1455448359),
  (3,1,1,30,1456064093),
  (3,1,1,30,1457266437),
  (3,1,1,30,1457883895),
  (3,1,1,30,1458489138),
  (3,1,1,30,1459097957),
  (3,1,1,30,1460798232),
  (3,1,1,30,1461601500),
  (3,1,1,30,1462464687),
  (3,1,1,30,1463835106),
  (3,1,1,30,1464511812),
  (3,1,1,30,1465117203),
  (3,1,1,30,1465731299),
  (3,1,1,30,1466337743),
  (3,1,2,30,1466943222),
  (3,1,2,30,1467552000),
  (3,1,5,30,1468175438),
  (3,1,5,30,1469559471),
  (3,1,5,30,1470470909),
  (3,1,5,30,1471633090),
  (3,1,5,30,1472284608),
  (3,1,5,30,1473220001),
  (3,1,5,30,1473862866),
  (3,1,5,30,1474467761);

INSERT INTO castles ("name", "description", "founded", "owner", "level", "hp") VALUES
  ('Falver','.',1447420077,1,5,'100'),
  ('Erdvor','.',1466869822,4,3,'100');

INSERT INTO comments ("title", "text", "article", "author", "added") VALUES
  ('Test','komentář',1,1,1435085477),
  ('Test','test test test test test',2,1,1435250522),
  ('Test 2','text',2,1,1441223636),
  ('Hotovo','Přechod již byl úspěšně dokončen.',3,1,1441372474),
  ('Test','text',6,1,1445197436);

INSERT INTO chat_messages ("message", "when", "user", "town", "monastery", "guild", "order") VALUES
  ('Vítejte v cechu',	1521573723,	3,	NULL,	NULL,	1,	NULL);

INSERT INTO events ("name", "description", "start", "end", "adventures_bonus", "work_bonus", "prayer_life_bonus", "training_discount", "repairing_discount", "shopping_discount") VALUES
  ('Oslavy založení','d',1467756000,1468360740,50,50,50,50,50,50);

INSERT INTO guilds ("name", "description", "level", "founded", "town", "money", "skill") VALUES
  ('Cech kupců z Myhru','.',2,1453484840,2,300,6);

INSERT INTO houses ("owner", "luxury_level", "brewery_level", "hp") VALUES
  (3,5,5,100);

INSERT INTO marriages ("user1", "user2", "status", "divorce", "proposed", "accepted", "term", "cancelled", "intimacy") VALUES
  (4,1,'active',0,1466241338,1466245558,1467450938,NULL,5),
  (3,6,'accepted',0,1475264924,1475264945,1792007340,NULL,0);

INSERT INTO messages ("subject", "text", "from", "to", "sent", "read") VALUES
  ('Test','Test message.',2,1,1434731668,TRUE),
  ('Test','Test message.',1,2,1434731668,TRUE),
  ('Zpráva','text text text',1,2,1434904922,TRUE),
  ('Orm','Lorem ipsum dota',1,3,1441278929,FALSE),
  ('Test','Just a test.',1,3,1441307001,FALSE),
  ('Test','tttttest',1,3,1444060591,FALSE),
  ('Povýšení','Již nějakou dobu jsi řádným občanem Nexendrie a proto jsi byl povýšen na Měšťana.',1,3,1447529598,FALSE),
  ('Dárek','Dostal jsi 1000 grošů a  Právo na založení města.',1,1,1447595907,TRUE),
  ('Povýšení','Byl jsi povýšen na měšťana.',1,3,1448473816,TRUE),
  ('Povýšení','Byl jsi povýšen na měšťana.',1,5,1468075669,FALSE);

INSERT INTO monasteries ("name", "leader", "town", "founded", "money", "altair_level", "library_level", "hp") VALUES
  ('Dům Jaly',2,1,1447251495,0,6,0,100);

INSERT INTO mounts ("name", "gender", "type", "owner", "price", "on_market", "birth", "hp", "damage", "armor") VALUES
  ('Mel','male',1,0,50,TRUE,1444838883,100,0,0),
  ('Erald','male',5,1,5000,FALSE,1444840086,95,6,4),
  ('Larna','female',1,0,50,TRUE,1444859395,100,0,0),
  ('Mil','male',1,0,50,TRUE,1444859656,100,0,0),
  ('Zimma','female',1,3,50,TRUE,1444859791,100,0,0),
  ('Ivlis','male',4,2,1800,FALSE,1446756290,95,3,5),
  ('Buris','male',2,0,127,TRUE,1447342669,100,0,1),
  ('Bila','female',2,3,127,FALSE,1447343032,95,1,3),
  ('Durhil','young',5,0,5000,TRUE,1447513936,100,3,2),
  ('Lana','female',4,4,400,FALSE,1455361161,95,3,5),
  ('Ihb An','male',3,0,300,TRUE,1465734704,100,1,0),
  ('Valdan','male',2,0,100,TRUE,1465735652,100,0,1);

INSERT INTO orders ("name", "description", "level", "founded", "money") VALUES
  ('Řád dračích jezdců','.',2,1465120352,400);

INSERT INTO polls ("question", "answers", "author", "added", "locked") VALUES
  ('Otázka','Možnost 1\nMožnost 2\nMožnost 3\nMožnost 4',1,1435673273,FALSE),
  ('Tvé oblíbené ORM','Doctrine\nLeanMapper\nNextras\\Orm',1,1441236118,FALSE),
  ('Tvůj oblíbený framework','Nette\nSymfony\nLaravel\nZend\nCodeIgniter',1,1444060844,FALSE);

INSERT INTO punishments ("user", "crime", "imprisoned", "released", "number_of_shifts", "count", "last_action") VALUES
  (2,'zlobil',1445172553,1445179141,5,5,1445178236);

INSERT INTO user_items ("item", "user", "amount", "worn", "level") VALUES
  (9,	1,	1,	TRUE,	3),
  (13,	1,	1,	TRUE,	3),
  (14,	1,	3,	FALSE,	0),
  (13,	3,	1,	TRUE,	3),
  (18,	1,	1,	TRUE,	1),
  (13,	2,	1,	TRUE,	0),
  (17,	2,	1,	FALSE,	0),
  (13,	4,	1,	FALSE,	0),
  (24,	4,	1,	FALSE,	0),
  (2,	4,	1,	FALSE,	0),
  (22,	2,	1,	TRUE,	0),
  (24,	3,	1,	TRUE,	3),
  (18,	4,	1,	FALSE,	0),
  (18,	2,	1,	TRUE,	0),
  (18,	3,	1,	TRUE,	1),
  (35,	4,	1,	TRUE,	2),
  (38,	4,	1,	TRUE,	2),
  (41,	4,	1,	TRUE,	2),
  (44,	1,	4,	FALSE,	0),
  (25,	3,	1,	FALSE,	0),
  (1,	3,	1,	FALSE,	0),
  (25,	1,	1,	FALSE,	0),
  (3, 1, 1, FALSE, 0);

INSERT INTO user_skills ("user", "skill", "level") VALUES
  (1,3,5),
  (1,6,5),
  (2,1,5),
  (2,6,5),
  (3,1,5),
  (3,6,5),
  (4,5,5),
  (1,7,10);

INSERT INTO users ("publicname", "password", "email", "joined", "last_active", "last_prayer", "last_transfer", "group", "infomails", "style", "gender", "life", "money", "town", "monastery", "prayers", "guild", "guild_rank", "order", "order_rank") VALUES
  ('Rahym','$2y$10$5rhQ8Puifw9YxQ8hdK.HCOeo5AW4EhLzLrDicx1TuE3TEs.tSUmVS','jakub.konecny2@seznam.cz',1435240277,1486402510,1446893589,1447251643,4,TRUE,'blue-sky','male',110,14357,1,2,2,NULL,NULL,NULL,NULL),
  ('Jakub','$2y$10$ejwYft0LbhlwhLz5vA07FOs2nNZGBb4IVpxkw7i5owXgjQ1JM6iF2','konecnyjakub01@gmail.com',1441219049,1489520003,NULL,1447528739,8,TRUE,'blue-sky','male',60,21001,2,NULL,0,1,4,NULL,NULL),
  ('Světlana','$2y$10$0a7MCizD1w6BECZvV7p4XOyA2aGyepJQPlpzJrFwvvURcSRzGpEL.','svetlana@localhost.k',1455360151,1486402684,1455466667,1455466659,5,FALSE,'dark-sky','female',60,14312,3,NULL,1,NULL,NULL,1,3),
  ('premysl','$2y$10$25fvAltDnlF.TOPTj8JlK.VC2BhFmijGJNjV5HxIz1LQ9Pj0L.LQK','premysl@localhost.k',1468050937,1468050937,NULL,NULL,9,FALSE,'blue-sky','male',60,30,2,NULL,0,NULL,NULL,NULL,NULL),
  ('kazimira','$2y$10$Om40QnY7ELgtedNugwwziOwVjn6mPDhFBQlXr1PR/h.w4Df0xBDZi','kazimira@localhost.k',1468051028,1479045690,NULL,NULL,12,FALSE,'blue-sky','female',60,30,2,NULL,0,NULL,NULL,NULL,NULL),
  ('bozena','$2y$10$Z7McIrRNP6pOt9xDfgp7/uPTjXO933l9ky/Ns9XEUsiu6YN0v2G4S','bozena@localhost.k',1495104741,1495104741,NULL,NULL,11,FALSE,'blue-sky','female',60,30,1,2,0,NULL,NULL,NULL,NULL);
