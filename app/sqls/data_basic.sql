-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `adventures` (`id`, `name`, `description`, `intro`, `epilogue`, `level`, `reward`, `event`) VALUES
  (1,	'Zloděj ovcí',	'Z blízké vesnice nedávno začaly mizet ovce. Zjisti, kdo nebo co za tím stojí a vyřeš to.',	'Vesničané tě dovedli na louku, kde viděli své ovce naposledy. Prozkoumáváš oblast a po chvíli narazíš na lidské i ovčí stopy směřující do nedalekého lesa. Jdeš do onoho lesa.',	'Po tvém návratu do vesnice ti šťastní vesničané děkovali a rychtář ti vyplatil odměnu 30 grošů.',	55,	30,	NULL),
  (2,	'Rychtářova dcera',	'Ve městě, do které jsi (přišel|přišla), panuje smutek. Rychtářova dcera byla unesena bandity. Strážníci jsou bezradní, možná by jsi jim mohl(a) pomoci.',	'S jistou dávkou štěstí se ti podařilo získat slyšení u rychtáře. Jakmile jsi mu sdělil svůj záměr zachránit jeho dceru, zmizelo trochu smutku z jeho tváře a jal se vyprávět ti, co se přihodilo. Také ti do nejmenší detailu popsal, jak vypadá jeho jediné dítě. Když domluvil, pustil jsi se hned do pátrání. Po několika hodinách bloudění po městě jsi našel prsten a následně klobouk, které jí patří. Následoval jsi tyto a další stopy a nakonec jsi došel do nedalekých hor, které jsou podle informací strážníků domovem banditů.',	'Po návratu do města okamžitě zamíříš k rychtáři. Ten ti upřímně poděkuje za záchranu své dcery a vyplatí ti slíbenou odměnu. Také ti řekne, že okamžitě pošle strážníky do hor pro bandity, aby je za jejich činy spravedlivě potrestal. Při odchodu se ještě jednou ohlédneš a zdá se ti, že spatříš náznak smutku v očích dívky. Slibuješ si, že se v tomto městě ještě někdy zastavíš ...',	55,	45,	NULL),
  (3,	'Nebezpečná stezka',	'Obchodníci cestující po důležité stezce jsou v poslední době příliš často přepadáváni lupiči. Místní pán nabízí odměnu tomu, kdo to zastaví.',	'Prozkoumal jsi danou cestu a přišel jsi s nápadem nastražit na lupiče past. Po cestě pojede drahý vůz a v místě přepadů bude mít \"nehodu\". Přijdou lupiči okrást cestující, ale v tu chvíli vyjdeš ty z keře a postavíš se jim. Místní pán nebyl tvým nápadem příliš nadšen, avšak nakonec svolil. Jal jsi se tedy jeho realizace. Skryješ se v keři a čekáš na příjezd vozu.',	'Po několika hodinách dojdeš zpět do města, kde ohlásíš, co se přihodilo. Je ti vyplacena slíbená odměna a na místo se vydávají strážníci, aby pochytali zbývající lupiče. Stezka bude na nějakou dobu opět bezpečná.',	100,	70,	NULL),
  (4,	'Velká loupež',	'Před několika dny došlo k loupeži na hradě místního pána. Ten nabízí odměnu za dopadení lupičů a vrácení jeho majetku.',	'Po prohlídce hradu nejsi příliš moudrý. Lupiči jsou zjevně mistři ve svém oboru, jelikož nezanechali žádné stopy, které by tě k nim dovedly. Nabízí se myšlenka, že práci provedl nějaký zlodějský cech. Zajdeš s ní za pánem a ten připustí, že má nepřítele, který by rád získal jeho majetek, a také tuší, koho si na krádež najal. Vydáváš se tedy do města, kde daný cech operuje, s nadějí, že je dopadneš.',	'Jak se nakonec ukáže, jsou to městské stráže, které přišli zatknout zloděje. Na chvíli se ti uleví. Pak ale zjistíš, že také považují za zloděje a chystají se tě také odvést. Stojí tě spoustu vysvětlování, ale nakonec tě pustí. Ty poté jdeš pro svého věrného oře a odjedeš vrátit pánovi jeho majetek. Ten je velmi šťastný a vyplatí ti slíbenou odměnu.',	100,	100,	NULL);

INSERT INTO `adventure_npcs` (`id`, `name`, `adventure`, `order`, `hitpoints`, `strength`, `armor`, `reward`, `encounter_text`, `victory_text`) VALUES
  (1,	'Zloděj',	1,	1,	20,	1,	0,	4,	'Po několika okamžicích spatříš podezřele vypadajícího muže. Ten tě také vidí a vrhá se na tebe s nepřátelským pohledem.',	'Jakmile jej přemůžeš, přiznává se ti, že to on odváděl ovce svých sousedů, aby je tu prodal nějakému obchodníkovi. Prý přišel při poslední povodni o vše. Rozhodl jsi se jej předvést před jeho pána, ať rozhodne o jeho osudu.'),
  (2,	'Bandita',	2,	1,	15,	1,	0,	3,	'Rozhlížíš a málem si nevšimneš, že na tebe útočí bandita. V poslední chvíli uhneš a tasíš zbraň.',	'Když porazíš banditu, snažíš se z něj dostat nějaké informace, které by ti pomohly najít unesenou, ale nepodaří se ti z něj dostat více než, že jsi udělal velkou chybu a že ostatní tě dostanou. Nezbývá ti tedy nic jiného, než to tu důkladně prohledat.'),
  (3,	'Bandita',	2,	2,	20,	2,	0,	5,	'Bohužel v nejbližším okolí vidíš jen keře a kamení. Z jednoho keře vyskočí bandita a vrhá se na tebe. Snad budeš mít tentokrát větší štěstí.',	'Jakmile se bandita vzdá, chystáš se jej vyslechnout, když v tom ...'),
  (4,	'Silný bandita',	2,	3,	24,	3,	1,	6,	'se, nevíš odkud, vynoří další bandita a vypadá, že se tě chystá rozčtvrtit.',	'Po menším boji nakonec porazíš i tohoto banditu a začneš s výslechem. Oba bandité jsou tak vyděšení z tvé síly, že ti okamžitě prozradí, že jejich zajatec je u jejich šéfa. Bez jakéhokoliv mučení ti také prozradí, kde je jejich šéf. Vydáváš se tedy za ním.'),
  (5,	'Šéf banditů',	2,	4,	30,	3,	1,	7,	'Bez jakéhokoliv problému najdeš šéfa banditů. Ten se, narozdíl od svých druhů, na tebe bezhlavě nevrhne, když tě spatří, místo toho se tě ptá, co tu pohledáváš. Řekneš mu, že jsi přišel zachránit rychtářovu dceru. On ti potvrdí, že ji nedávno unesli a také ti řekne, že si je na to najal nějaký nepřítel jejího otce. Nakonec ti klidný hlasem poví, že ty už se odtud nedostaneš. Dokaž mu, že se mýlí!',	'Po tuhém boji nakonec šéf banditů padá vyčerpáním k boji. Ve stejném okamžiku vylézá z nedalekého keře asi sedmnáctiletá dívka, která odpovídá popisu rychtářovy dcery. Překotně ti děkuje za svou záchranu a dokonce tě políbí na tvář. Nakonec usednete na tvého věrného oře a vydáváte se zpět do jejího města.'),
  (6,	'Lupič',	3,	1,	22,	2,	1,	5,	'Ten nakonec přijede a má plánovanou nehodu. Vidíš z lesa přicházet menší skupinku lupičů. Jdou k přímo k vozu, tebe si nevšimnou. Když dojdou, smějí se nehodě a chystají se otevřít vůz, když je zastavíš. Ten nejsilnější se na tebe vrhá.',	'Po vítězství nemáš ani možnost si oddechnout, protože se na okamžitě řítí další.'),
  (7,	'Lupič',	3,	2,	24,	2,	1,	5,	'Hodí po tobě nějaké malé kovové kolo s hroty. Jsi překvapen, ale v poslední chvíli se vzpamatuješ a uhneš. To jej ale neodradilo a útočí na tebe šavlí. Braň se.',	'Lupič padá vyčerpaný k zemi a ostatní se snaží uprchnout. Podaří se ti je všechny zastavit a začneš jim pokládat otázky. Kolik jich je, kde mají základnu ... Trochu se ošívají, ale nakonec ti vše prozradí. Jak jsi předpokládal, sídlí v blízké lese. Vydáváš se tam tedy.'),
  (8,	'Jednooký lupič',	3,	3,	27,	3,	1,	6,	'Prohlížíš si les. Je v něm mnoho stromů, lupiči se tu mohou dobře skrývat. Musíš být tedy opatrný. Po chvíli slyšíš šustnutí a současně vylézá ze svého úkrytu lupič s páskou přes oko. Jde přímo k tobě. Jeho chůze je pomalá, klidná. Kdyby nevytahoval meč, myslel by sis, že jen prochází kolem. Když je asi metr od tebe, vydá hrozivý zvuk a vrhá se na tebe.',	'Bojoval dobře, ale také padá k zemi. Pokračuješ k základně bandy.'),
  (9,	'Jednoruký lupič',	3,	4,	32,	3,	0,	7,	'Nejdeš ani pět minut a útočí na tebe další lupič, tentokrát mu chybí jedna ruka. To bude krátký boj, říkáš si.',	'Také byl, ale ve chvíli, kdy je porazíš, k vám přichází skupina 7 po zuby ozbrojený lupičů. Ty neporazíš. Vzdáváš se, oni tě svážou a odvádějí tě do své základny.'),
  (10,	'Šéf lupičů',	3,	5,	40,	3,	3,	10,	'Jakmile dojdete, shodí tě na zem a kopou do tebe. Po chvíli je to ale přestane bavit, tak přestanou a rozcházejí se. Tři si jdou lehnout k ohništi, dva odejdou za jinými úkoly. Zůstává u tebe jen jejich šéf a jeden další lupič. Vyčkáváš na příležitost k útěku. Ta nastane o půl hodiny později, když lupiči u ohniště konečně usnou a další lupič odchází. Ty nenápadně rozvážeš provazy a vezmeš si svou zbraň. V tu chvíli si tě ale všimne šéf a zaútočí na tebe.',	'Po tuhém boji jej porazíš a on padá. Místo aby spadl na zem však dopadá na nějakou tyč, které jej probodne. Jeho křik probouzí spáče u ohniště. Ty ale nečekáš, až k tobě přijdou a utíkáš pryč z lesa.'),
  (11,	'Opilec',	4,	1,	25,	3,	1,	3,	'Cesta ti velmi rychle uběhla, ani jsi nenarazil na žádné lapky. Po příjezdu okamžitě zamíříš do místního hostince se posilnit a taky získat nějaké informace o lupičích. Žádný stůl však není prázdný a ty si musíš sednout k muži, který silně zapáchá. Nejen potem ale i alkoholem. Konečně ti obsluha donese objednané jídlo a ty se do něj pustíš. Po chvíli však tvůj spolustolovník na tebe začne bezdůvodně řvát a dokonce ti dá facku.',	'Stačilo mu několik ran a hned padal k zemi. Ty si poté s klidem opět sedneš a věnuješ se svému jídlu. Po chvíli si k tobě přisedne mladá a nezapáchající žena. Obdivuje se tvé síle a když se jí svěříš se svou misí, ráda ti poradí, kde cech sídlí. Prý je to v podzemí, kde si postavili síť kanálů a dokonce i několik příbytků. Nezní to příliš hezky, ale práce je práce a tak se tam vydáš. '),
  (12,	'Hlídač',	4,	2,	27,	3,	2,	4,	'Nakonec dojdeš ke vstupu, o kterém ti řekla ta žena. Avšak ve chvíli, kdy chceš vstoupit, na tebe vyskočí, ani nevíš odkud, hlídač.',	'Po chvíli ale už leží na zemi a vykládá ti o tom, že vůbec nevíš, do čeho se pouštíš a že se odtud nedostaneš. Ty jej ignoruješ a vstupuješ do podzemí. Je tu však spousta cest a ty nevíš, kterou se vydat. Následuješ tedy svůj instinkt a jednu zvolíš. Snad je to ta správná.'),
  (13,	'Hlídač',	4,	3,	30,	3,	2,	5,	'Po několika minutách si zjistíš, že tato ulička je slepá. Chystáš se tedy vrátit a vybrat si jinou cestu. Vtom ti ale zastoupí cestu hlídač.',	'Nebyl ale příliš silný, proto jej rychle porazíš a jdeš dál. Tentokrát už v klidu dojdeš ke křižovatce a vybíráš si jinou chodbu. Doufáš, že tentokrát už dojdeš ke svému cíli.'),
  (14,	'Zloděj',	4,	4,	30,	3,	3,	6,	'Zdá se, že nyní byla tvá volba lepší. Cesta tě dovede až k menšímu domku. Vypadá zchátrale a nejsou u něj žádné stráže. Rozhodneš se jej prozkoumat. Avšak jen co vejdeš dovnitř, začne ze stropu padat hromada dýk. Jen taktak se ti podaří uhnout. Nedostaneš ani čas se vzpamatovat, protože téměř okamžitě se na tebe vrhá zloděj.',	'Rychle jej zneškodníš a začneš s jeho výslechem. Začne s obvyklými řečmi, že děláš velkou chybu, ale nakonec z něj dostaneš i, že opravdu okradli místního pána a že lup je prozatím zde. Po prozrazení této informace však omdlí.'),
  (15,	'Zlodějka',	4,	5,	33,	3,	3,	7,	'Neztrácíš tedy čas a začneš s průzkumem domu. Zdá se, že slouží zlodějům jako skladiště, protože se zde válí spousta balíčků a dokonce i rozbitých věcí. Po chvílí slyšíš, jak na tebe letí nůž. Soustředíš se a chytíš jej. Zaraduješ se, ale o vteřinu později už u tebe stojí zlodějka a útočí na tebe.',	'Po několika minutách boje se ale zhroutí na zem a vzdává se ti. Ty skončíš s bojem a začneš jí pokládat otázky. Kde se nachází lup, který hledáš, kdo jej hlídá atd. Ona ti, s přerývaných dechem, odpoví, že to hledáš, chrání osobně jejich vůdce, kterého ale jistě neporazíš. A dodává, že i kdyby se ti to nějakým zázrakem podařilo, živý se odtud nedostaneš. Ty se nenecháš rozhodit a vydáš se za vůdcem zlodějů.'),
  (16,	'Vůdce zlodějů',	4,	6,	37,	4,	4,	12,	'Nemáš vůbec problém jej najít a nikdo ti ani nestojí v cestě. Začínáš tušit, že na slovech zlodějky asi něco bude. Nemáš čas ale příliš přemýšlet, protože vůdce zlodějů si tě všimne a míří k tobě. Během jeho chůze si jej prohlížíš. Je štíhlý a mrštný. Nebude lehkým soupeřem. Než si ale stihneš promyslet svou strategii, už na tebe útočí.',	'Po dlouhém boji nakonec pořádně udeříš a tvůj protivník se začne svíjet bolestí. Ptáš se jej, kde jsou věci, které ukradl místnímu pánovi. On ukáže na jednu hromádku poblíž. Prohlížíš si pozorně dané věci a shledáš, že mluví pravdu. Pobereš tedy dané věci a chystáš se k odchodu. V tu chvíli ale ze všech koutů vyleze asi deset zloděj s hrozivým pohledem v očích. Lekneš se a začneš rychle přemýšlet jak se dostat ven. Ke dveřím se nestihneš dostat včas a okna žádná nevidíš. Z přemýšlení tě vyruší hluk u dveří.');

INSERT INTO `groups` (`id`, `name`, `single_name`, `female_name`, `level`, `path`, `max_loan`) VALUES
  (0,	'Vládci',	'král',	'královna',	10001,	'tower',	2000),
  (1,	'Korunní rada',	'kníže',	'kněžna',	10000,	'tower',	2000),
  (2,	'Markrabata',	'markrabě',	'markraběnka',	1000,	'tower',	1500),
  (3,	'Panstvo',	'lord',	'lady',	600,	'tower',	1500),
  (4,	'Vyšší klérus',	'velekněz',	'velekněžka',	550,	'church',	1500),
  (5,	'Rytíři',	'rytíř',	'dáma',	400,	'tower',	700),
  (6,	'Duchovní',	'kněz',	'kněžka',	350,	'church',	500),
  (7,	'Rychtáři',	'rychtář',	'rychtářka',	345,	'city',	500),
  (8,	'Konšelé',	'konšel',	'konšelka',	300,	'city',	500),
  (9,	'Měšťané',	'měšťan',	'měšťanka',	100,	'city',	300),
  (10,	'Akolyté',	'akolyta',	'akolyta',	90,	'church',	300),
  (11,	'Mnišstvo',	'bratr',	'sestra',	55,	'church',	70),
  (12,	'Sedláci',	'sedlák',	'selka',	50,	'city',	70),
  (13,	'Cizinci',	'cizinec',	'cizinka',	0,	'city',	0),
  (14,	'Vězni',	'vězeň',	'vězeňkyně',	0,	'city',	0);

INSERT INTO `guild_ranks` (`id`, `name`, `income_bonus`, `guild_fee`) VALUES
  (1,	'učedník',	5,	50),
  (2,	'tovaryš',	5,	75),
  (3,	'mistr',	10,	110),
  (4,	'cechmistr',	10,	135);

INSERT INTO `items` (`id`, `name`, `description`, `price`, `shop`, `type`, `strength`) VALUES
  (1,	'Jednoduchý náramek',	'Jednoduchý kamenný náramek',	4,	2,	'item',	0),
  (2,	'Měšec',	'Obyčejný měšec, vleze se do něj sotva 20 mincí',	16,	1,	'item',	0),
  (3,	'Dřevěný meč',	'Obyčejný dřevěný meč',	5,	3,	'weapon',	1),
  (4,	'Železný meč',	'Obyčejný meč ze železa',	21,	3,	'weapon',	2),
  (5,	'Košile',	'Čistá bílá košile. Nosí se při slavnostních příležitostech nebo v krajní nouzi i v boji.',	10,	6,	'armor',	1),
  (6,	'Stříbrný meč',	'Meč ze zvláštní stříbrné slitiny',	52,	3,	'weapon',	3),
  (7,	'Zlatý meč',	'Meč z kvalitního zlata.',	86,	3,	'weapon',	4),
  (8,	'Sekyrka',	'Nejobyčejnější sekera',	21,	3,	'weapon',	2),
  (9,	'Válečná sekera',	'Kvalitní dvoubřitvá sekera',	126,	3,	'weapon',	5),
  (10,	'Vycpávaná zbroj',	'Dvouvrstvá kožená zbroj',	17,	6,	'armor',	2),
  (11,	'Kroužková košile',	'Kvalitní kroužkové brnění',	48,	6,	'armor',	3),
  (12,	'Kyrys',	'Kvalitní několikavrstvé plátové brnění',	91,	6,	'armor',	4),
  (13,	'Pozlacený kyrys',	'Artefaktový kyrys vyrobený kovářským mistrem a očarovaný mocným čarodějem',	153,	6,	'armor',	5),
  (14,	'Slabý elixír zdraví',	'Nejslabší elixír. Obnoví 2 životy',	8,	5,	'potion',	2),
  (15,	'Právo na založení města',	'Dokument vydaný královnou opravňující k založení města či vesnice',	999,	NULL,	'charter',	0),
  (16,	'Střední elixír zdraví',	'Silnější elixír, který obnoví 5 životů',	23,	5,	'potion',	5),
  (17,	'Velký elixír zdraví',	'Silný elixír, který obnoví 7 životů',	33,	5,	'potion',	7),
  (18,	'Kožená přilba',	'Nejjednodušší přilba, poskytuje jen slabou ochranu',	25,	6,	'helmet',	1),
  (19,	'Kopí',	'Obyčejné kopí',	5,	3,	'weapon',	1),
  (20,	'Pilum',	'Krátké, avšak nebezpečné kopí',	52,	3,	'weapon',	3),
  (21,	'Sudlice',	'Silná dřevcová zbraň',	84,	3,	'weapon',	4),
  (22,	'Kropáč',	'Dlouhá palice s ostny',	126,	3,	'weapon',	5),
  (23,	'Bojový nůž',	'Krátká bodná a řezná zbraň',	5,	3,	'weapon',	1),
  (24,	'Platinový meč',	'Velmi kvalitní dlouhý meč',	126,	3,	'weapon',	5),
  (25,	'Ozdobný měšec',	'Měšec s jednoduchou kresbou, pojme asi 50 mincí',	35,	1,	'item',	1),
  (26,	'Vandrákův plášť',	'Potrhaný, lehký, starý plášť. Poskytuje velmi slabou ochranu',	17,	6,	'armor',	1),
  (27,	'Kápě učedníka',	'Obyčejné černé roucho používané čaroději - učedníky',	15,	4,	'armor',	1),
  (28,	'Plášť zloděje',	'Lehký, černý plášť, který neomezuje pohyb a zároveň poskytuje ochranu',	36,	6,	'armor',	2),
  (29,	'Kápě mistra zloděje',	'Dlouhý, hebký, fialový plášť',	130,	6,	'armor',	3),
  (30,	'Hůlka učedníka',	'Krátká hůlka z osikového dřeva pro učedníky',	13,	4,	'weapon',	1),
  (31,	'Čepice učedníka',	'Malá, šedá, nepříliš pohodlná čepice',	36,	4,	'helmet',	1),
  (32,	'Čapka',	'Stará čepice s několika dírkami',	35,	6,	'helmet',	1),
  (33,	'Hůlka čaroděje',	'Hůlka z borovicového dřeva. Používají jej zkušenější čarodějové',	35,	4,	'weapon',	2),
  (34,	'Hůl mistra',	'Dlouhá, pevná hůl z dubového dřeva',	79,	4,	'weapon',	3),
  (35,	'Arcimágova hůl',	'Dlouhá bílá hůl vyrobená z mramoru',	148,	4,	'weapon',	4),
  (36,	'Čarodějova kápě',	'Zelená, pohodlná kápě používaná čaroději',	26,	4,	'armor',	2),
  (37,	'Kápě mistra čaroděje',	'Modrá kápě používaná mistry čaroději',	64,	4,	'armor',	3),
  (38,	'Arcimágův kabát',	'Zdobený fialový kabát',	127,	4,	'armor',	4),
  (39,	'Čarodějova čepice',	'Žlutá, pohodlná čepice používaná čaroději',	49,	4,	'helmet',	2),
  (40,	'Klobouk mistra',	'Velký šedý klobouk používaný mistry čaroději',	73,	4,	'helmet',	3),
  (41,	'Arcimágův klobouk',	'Zdobený černý klobouk',	103,	4,	'helmet',	4),
  (42,	'Malé srdce',	'Zvýší důvěrnost manželů o 1',	17,	NULL,	'intimacy_boost',	1),
  (43,	'Střední srdce',	'Zvýší důvěrnost manželů o 2',	28,	NULL,	'intimacy_boost',	2),
  (44,	'Velké srdce',	'Zvýší důvěrnost manželů o 5',	83,	NULL,	'intimacy_boost',	5);

INSERT INTO `item_sets` (`id`, `name`, `weapon`, `armor`, `helmet`, `stat`, `bonus`) VALUES
  (1,	'Odvedencova sada',	3,	5,	18,	'hitpoints',	3),
  (2,	'Sada mladého čaroděje',	30,	27,	31,	'damage',	2),
  (3,	'Vandrákova sada',	23,	26,	32,	'armor',	2),
  (4,	'Čarodějova sada',	33,	36,	39,	'damage',	4),
  (5,	'Mistr čaroděj',	34,	37,	40,	'damage',	6),
  (6,	'Arcimágova sada',	35,	38,	41,	'damage',	8);

INSERT INTO `jobs` (`id`, `name`, `description`, `help`, `count`, `award`, `shift`, `level`, `needed_skill`, `needed_skill_level`) VALUES
  (1,	'Rybář',	'Rybář loví v řece ryby a následně je prodává.',	'Lovit můžeš každých 70 minut a za 1 ulovenou rybu dostaneš %reward%. Pamatuj ale, že lov se nemusí vždy podařit!',	0,	2,	70,	50,	1,	0),
  (2,	'Horník',	'Horník v podzemí těží nerosty.',	'Běž hledat nerosty. Musíš jich najít aspoň %count%, abys dostal %reward%. Jestli to nezvládneš, tak si mě nepřej!',	20,	80,	50,	50,	4,	1),
  (3,	'Pastýř',	'Pastýř se stará o zvířata.',	'Postarej se o tohle zvíře na 2 hodiny. Pokud se alespoň %count% nic nestane, dostaneš %reward%.',	13,	70,	120,	50,	2,	0),
  (4,	'Strážník',	'Strážník dohlíží na klid a pořádek v ulicích města.',	'Vydej se hlídat ulice města. Za každou hodinu, kdy se nic nestane, dostaneš %reward%.',	0,	4,	60,	100,	5,	1),
  (5,	'Písař',	'Písař píše zápisy a listiny. Také přepisuje knihy.',	'Tak už se dej do psaní. Musíš zvládnout alespoň %count% směn po hodině a půl, aby sis zasloužil %reward%. Jestli tam ale bude mnoho chyb, tak ti to strhnu!',	15,	180,	90,	100,	3,	1),
  (6,	'Kupec',	'Kupec nakupuje zboží a následně jej jinde za vyšší cenu prodává.',	'Nakup nějaké zboží a pokus se jej prodat v sousedním městě.',	0,	8,	120,	100,	6,	1),
  (7,	'Bankovní úředník',	'Banka Žajských potřebuje posily do místní pobočky. Nejvíce jsou potřeba úředníci na příjem a výdej hotovosti. Požadují se zkušenosti s obchodní činností.',	'Musíš obsluhovat klienty přicházející do banky. Aby sis zasloužil %reward%, musíš zvládnout alespoň %count% hodin.',	20,	220,	60,	100,	6,	2),
  (8,	'Žoldnéř',	'Pohraniční oblasti byly napadeny našimi sousedy a místní páni nemají dostatek mužů, aby odrazili nepřátele. Slibují odměnu za pomoc.',	'Braň vesnici proti útokům cizáků.',	0,	10,	90,	400,	5,	2),
  (9,	'Pomocník v kuchyni',	'Místní pán má nedostatek lidí v kuchyni. Budeš jen nosit či upravovat jednotlivé suroviny.',	'Přines to a tamto. A pospěš si nebo nedostaneš %reward% za tuhle hodinu!',	0,	3,	60,	50,	10,	0),
  (10,	'Kuchař',	'Jeden z kuchařů místního pána je nemocný. Dokážeš jej zastoupit?',	'Pán chce k večeři pečené holuby. Nekoukej tak a dej se do práce!',	12,	110,	60,	90,	10,	1),
  (11,	'Tělesný strážce',	'Významný obchodník potřebuje ochranu během obchodní cesty.',	'Braň obchodníka!',	20,	250,	45,	400,	5,	3),
  (12,	'Pekař',	'Pekař peče a prodává pečivo.',	'Dej se do pečení. Za každý kus dostaneš %reward%.',	0,	6,	90,	100,	10,	2),
  (13,	'Knihovník',	'V místní akademie je neobvykle mnoho studentů, kteří dychtí po nových znalostech. Sežeň jim potřebné studijní materiály z archivu a postarej se o nezbytnou administrativu.',	'Přichází další student. Běž mu pomoct.',	20,	200,	45,	100,	3,	2);

INSERT INTO `job_messages` (`id`, `job`, `success`, `message`) VALUES
  (1,	1,	1,	'Ulovil(a) jsi a prodal(a) 1 rybu.'),
  (2,	1,	0,	'Nepodařilo se ti nic ulovit.'),
  (3,	1,	0,	'Někdo ti ukradl chycenou rybu.'),
  (4,	2,	1,	'Vytěžil(a) jsi 1 nerost.'),
  (5,	2,	0,	'Nic jsi nenašel.'),
  (6,	3,	1,	'Svěřené ovci se nic nestalo.'),
  (7,	3,	1,	'Svěřené krávě se nic nestalo.'),
  (8,	3,	0,	'Svěřená ovce se zaběhla.'),
  (9,	3,	0,	'Svěřená kráva se zaběhla.'),
  (10,	4,	1,	'Během tvé služby se nic nestalo.'),
  (11,	4,	1,	'Chytil(a) jsi 1 zloděje.'),
  (12,	4,	0,	'Unikl ti zloděj.'),
  (13,	4,	0,	'Zranili tě protestující občaně.'),
  (14,	5,	1,	'Napsal(a) jsi zápis bez jedniné chyby.'),
  (15,	5,	1,	'Přepsal(a) jsi 1 kapitolu knihy.'),
  (16,	5,	1,	'Napsal(a) jsi zápis z městské rady.'),
  (17,	5,	0,	'Rozlil se ti inkoust.'),
  (18,	5,	0,	'Udělal(a) jsi v zápisu spostu chyb.'),
  (19,	2,	0,	'Praštil(a) jsi se do hlavy.'),
  (20,	4,	1,	'Uklidnil(a) jsi rozzuřený dav.'),
  (21,	6,	1,	'Podařilo se ti prodat zboží.'),
  (22,	6,	0,	'Na cestě do sousedního města tě přepadli lupiči.'),
  (23,	6,	0,	'Tvé zboží nechtěl nikdo koupit.'),
  (24,	9,	0,	'Přinesl(a) jsi špatnou surovinu.'),
  (25,	9,	0,	'Poranil(a) jsi se při krájení.'),
  (26,	11,	1,	'Ochránil(a) jsi obchodníka před lupiči.'),
  (27,	11,	1,	'Prošli jste bez obtíží tímto úsekem.'),
  (28,	11,	0,	'Lupiči ukradli část zboží.'),
  (29,	11,	0,	'Při boji jsi poškodil(a) část zboží.'),
  (30,	12,	1,	'Upekl(a) a prodal(a) jsi 1 bochník chleba.'),
  (31,	12,	1,	'Upekl(a) a prodal(a) jsi 1 koblihu.'),
  (32,	12,	1,	'Upekl(a) a prodal(a) jsi 1 koláč.'),
  (33,	12,	1,	'Upekl(a) a prodal(a) jsi 1 makový závin.'),
  (34,	12,	0,	'Tvé pečivo nikdo nechtěl koupit.'),
  (35,	12,	0,	'Tvé pečivo nebylo poživatelné.'),
  (36,	13,	0,	'Přinesl(a) jsi špatnou knihu.'),
  (37,	13,	0,	'Hledal(a) jsi knihu příliš dlouho, student odešel.');

INSERT INTO `meals` (`id`, `name`, `message`, `price`, `life`) VALUES
  (1,	'Placka',	'Tady to je. Nesnáším výrobu ovesných placek, ale za těch 5 grošů to udělám.',	5,	2),
  (2,	'Ovesná kaše',	'Už se to nese. Ovesná kaše!',	7,	2),
  (3,	'Pstruh',	'Právě upečený pstruh s bramborama. Nech si chutnat.',	14,	3),
  (4,	'Chléb',	'Tu máš bochník chleba.',	5,	2),
  (5,	'Voda',	'Tady máš sklenici vody. Nic zvláštního? Ano, ale co bys čekal za 2 groše!',	2,	1);

INSERT INTO `mount_types` (`id`, `name`, `female_name`, `young_name`, `description`, `level`, `damage`, `armor`, `price`) VALUES
  (1,	'Osel',	'Oslice',	'Oslátko',	'.',	50,	0,	0,	50),
  (2,	'Kůň',	'Klisna',	'Hříbě',	'.',	100,	0,	1,	100),
  (3,	'Velbloud',	'Velbloudice',	'Velbloudě',	'.',	100,	1,	0,	300),
  (4,	'Jednorožec',	'Jednorožčice',	'Jednorožče',	'.',	400,	1,	2,	1200),
  (5,	'Drak',	'Dračice',	'Dráče',	'.',	600,	3,	2,	5000);

INSERT INTO `order_ranks` (`id`, `name`, `adventure_bonus`, `order_fee`) VALUES
  (1,	'zbrojnoš',	5,	65),
  (2,	'rytíř',	5,	90),
  (3,	'mistr',	10,	125),
  (4,	'velmistr',	10,	150);

INSERT INTO `permissions` (`id`, `resource`, `action`, `group`) VALUES
  (1,	'site',	'manage',	10),
  (2,	'poll',	'add',	1),
  (3,	'poll',	'vote',	12),
  (4,	'article',	'add',	10),
  (5,	'article',	'edit',	1),
  (6,	'comment',	'add',	12),
  (7,	'comment',	'delete',	1),
  (8,	'group',	'list',	1),
  (9,	'group',	'edit',	1),
  (10,	'user',	'list',	1),
  (11,	'user',	'edit',	1),
  (12,	'user',	'ban',	1),
  (13,	'content',	'list',	1),
  (14,	'content',	'add',	1),
  (15,	'content',	'edit',	1),
  (16,	'site',	'settings',	1),
  (17,	'content',	'gift',	1),
  (18,	'event',	'add',	1),
  (19,	'event',	'edit',	1),
  (20,	'event',	'delete',	1),
  (21,	'content',	'delete',	1),
  (22,	'poll',	'list',	1),
  (23,	'event',	'list',	1),
  (24,	'town',	'elect',	9),
  (25,	'town',	'manage',	1);

INSERT INTO `shops` (`id`, `name`, `description`) VALUES
  (1,	'Hamalův všehoobchod',	'popisek'),
  (2,	'Jackovo klenotnictví',	'Jen se podívejte na mou bohatou nabídku šperků.'),
  (3,	'Zbyslavovy zbraně',	'Jen pojďte dál, mám ty nejlepší zbraně v celé Nexendrii!'),
  (4,	'Kazimířin magický obchůdek',	'Mám vše, co čaroděj potřebuje!'),
  (5,	'Meklavovy lektvary',	'Chystáte se na dobrodružství a potřebujete lektvary? Jste na správné místě!'),
  (6,	'Vaškovy zbroje',	'Dobrá zbroj je důležitá pro vítězství v souboji.');

INSERT INTO `skills` (`id`, `name`, `price`, `max_level`, `type`, `stat`, `stat_increase`) VALUES
  (1,	'Rybolov',	15,	5,	'work',	NULL,	0),
  (2,	'Péče o zvířata',	15,	5,	'work',	NULL,	0),
  (3,	'Čtení a psaní',	35,	5,	'work',	NULL,	0),
  (4,	'Těžba',	15,	5,	'work',	NULL,	0),
  (5,	'Zacházení se zbraněmi',	30,	5,	'work',	NULL,	0),
  (6,	'Obchodování',	35,	5,	'work',	NULL,	0),
  (7,	'Výdrž',	50,	10,	'combat',	'hitpoints',	5),
  (8,	'Houževnatost',	50,	5,	'combat',	'armor',	1),
  (9,	'Síla',	50,	5,	'combat',	'damage',	1),
  (10,	'Vaření',	20,	5,	'work',	NULL,	0);

INSERT INTO `towns` (`id`, `name`, `description`, `founded`, `owner`, `price`, `on_market`) VALUES
  (1,	'Velehrad',	'Starobylé hlavní město',	1429779664,	0,	5000,	0),
  (2,	'Myhr',	'Největší město na severovýchodě',	1429779664,	1,	5000,	0),
  (3,	'Světlohvozd',	'Malá vesnice ve středu země',	1429779664,	0,	5000,	0),
  (4,	'Bělehrad',	'Starobylé město na jihu země. Kdysi hlavní město (polo)samostatného knížectví, dnes královské a korunovační město',	1429779664,	0,	5000,	0),
  (5,	'Lípa',	'Vesnička na jihu země',	1429779664,	0,	5000,	1),
  (6,	'Žalecký Brod',	'Větší vesnice na východě země',	1429779664,	2,	5000,	0),
  (7,	'Dazluk',	'Přístavní město na západě',	1429779664,	0,	5000,	1),
  (8,	'Bílé Louky',	'Větší vesnice na jihu',	1429779664,	0,	5000,	1),
  (9,	'Vrchní Lhota',	'Vesnička na severovýchodě země',	1447601504,	1,	1000,	0),
  (10,	'Velké Běliny',	'Větší vesnice na jihu země',	1455361687,	4,	5000,	0),
  (11,	'Roden',	'Vesnice na západě země',	1463914056,	0,	5000,	1);

INSERT INTO `users` (`id`, `publicname`, `password`, `email`, `joined`, `last_active`, `last_prayer`, `last_transfer`, `group`, `infomails`, `style`, `gender`, `life`, `money`, `town`, `monastery`, `prayers`, `guild`, `guild_rank`, `order`, `order_rank`) VALUES
  (0,	'Vladěna',	'$2y$10$SKkWGjWJBlDDZcviLt0SXu5fNEaxsRAXlP82.nLZfq9gWN7n.qKe2',	'admin@localhost',	1429779664,	1475089811,	NULL,	NULL,	0,	0,	'blue-sky',	'female',	60,	560132,	1,	NULL,	0,	NULL,	NULL,	NULL,	NULL),
  (1,	'Trimadyl z Myhru',	'$2y$10$SKkWGjWJBlDDZcviLt0SXu5fNEaxsRAXlP82.nLZfq9gWN7n.qKe2',	'jakub.konecny2@centrum.cz',	1429779664,	1475089811,	NULL,	1455366455,	1,	1,	'dark-sky',	'male',	110,	18229,	2,	NULL,	0,	NULL,	NULL,	1,	4);

INSERT INTO `castles` (`id`, `name`, `description`, `founded`, `owner`, `level`, `hp`) VALUES
  (1,	'Dračí hrad',	'Dračí hrad je již od nepaměti sídlem panovníků Nexendrie.',	1429779664,	0,	5,	'100');

INSERT INTO `monasteries` (`id`, `name`, `leader`, `town`, `founded`, `money`, `altair_level`,`library_level`, `hp`) VALUES
  (1,	'Pokusný klášter',	0,	3,	1446628777,	1001,	1, 0,	100);

-- 2016-09-25 09:08:14