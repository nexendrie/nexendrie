<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitData extends AbstractMigration {
  public function up(): void {
    $this->query("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
    $this->table("adventures")
      ->insert([
        [
          "id" => 1, "name" => "Zloděj ovcí", "description" => "Z blízké vesnice nedávno začaly mizet ovce. Zjisti, kdo nebo co za tím stojí a vyřeš to.", "intro" => "Vesničané tě dovedli na louku, kde viděli své ovce naposledy. Prozkoumáváš oblast a po chvíli narazíš na lidské i ovčí stopy směřující do nedalekého lesa.", "epilogue" => "Po tvém návratu do vesnice ti šťastní vesničané děkovali a rychtář ti vyplatil odměnu 30 grošů.", "level" => 55, "reward" => 30, "event" => null,
        ],
        [
          "id" => 2, "name" => "Rychtářova dcera", "description" => "Ve městě, do které jsi (přišel|přišla), panuje smutek. Rychtářova dcera byla unesena bandity. Strážníci jsou bezradní, možná by jsi jim mohl(a) pomoci.", "intro" => "S jistou dávkou štěstí se ti podařilo získat slyšení u rychtáře. Jakmile jsi mu sdělil svůj záměr zachránit jeho dceru, zmizelo trochu smutku z jeho tváře a jal se vyprávět ti, co se přihodilo. Také ti do nejmenší detailu popsal, jak vypadá jeho jediné dítě. Když domluvil, pustil jsi se hned do pátrání. Po několika hodinách bloudění po městě jsi našel prsten a následně klobouk, které jí patří. Následoval jsi tyto a další stopy a nakonec jsi došel do nedalekých hor, které jsou podle informací strážníků domovem banditů.", "epilogue" => "Po návratu do města okamžitě zamíříš k rychtáři. Ten ti upřímně poděkuje za záchranu své dcery a vyplatí ti slíbenou odměnu. Také ti řekne, že okamžitě pošle strážníky do hor pro bandity, aby je za jejich činy spravedlivě potrestal. Při odchodu se ještě jednou ohlédneš a zdá se ti, že spatříš náznak smutku v očích dívky. Slibuješ si, že se v tomto městě ještě někdy zastavíš ...", "level" => 55, "reward" => 45, "event" => null,
        ],
        [
          "id" => 3, "name" => "Nebezpečná stezka", "description" => "Obchodníci cestující po důležité stezce jsou v poslední době příliš často přepadáváni lupiči. Místní pán nabízí odměnu tomu, kdo to zastaví.", "intro" => "Prozkoumal jsi danou cestu a přišel jsi s nápadem nastražit na lupiče past. Po cestě pojede drahý vůz a v místě přepadů bude mít \"nehodu\". Přijdou lupiči okrást cestující, ale v tu chvíli vyjdeš ty z keře a postavíš se jim. Místní pán nebyl tvým nápadem příliš nadšen, avšak nakonec svolil. Jal jsi se tedy jeho realizace. Skryješ se v keři a čekáš na příjezd vozu.", "epilogue" => "Po několika hodinách dojdeš zpět do města, kde ohlásíš, co se přihodilo. Je ti vyplacena slíbená odměna a na místo se vydávají strážníci, aby pochytali zbývající lupiče. Stezka bude na nějakou dobu opět bezpečná.", "level" => 100, "reward" => 70, "event" => null,
        ],
        [
          "id" => 4, "name" => "Velká loupež", "description" => "Před několika dny došlo k loupeži na hradě místního pána. Ten nabízí odměnu za dopadení lupičů a vrácení jeho majetku.", "intro" => "Po prohlídce hradu nejsi příliš moudrý. Lupiči jsou zjevně mistři ve svém oboru, jelikož nezanechali žádné stopy, které by tě k nim dovedly. Nabízí se myšlenka, že práci provedl nějaký zlodějský cech. Zajdeš s ní za pánem a ten připustí, že má nepřítele, který by rád získal jeho majetek, a také tuší, koho si na krádež najal. Vydáváš se tedy do města, kde daný cech operuje, s nadějí, že je dopadneš.", "epilogue" => "Jak se nakonec ukáže, jsou to městské stráže, které přišli zatknout zloděje. Na chvíli se ti uleví. Pak ale zjistíš, že také považují za zloděje a chystají se tě také odvést. Stojí tě spoustu vysvětlování, ale nakonec tě pustí. Ty poté jdeš pro svého věrného oře a odjedeš vrátit pánovi jeho majetek. Ten je velmi šťastný a vyplatí ti slíbenou odměnu.", "level" => 100, "reward" => 100, "event" => null,
        ],
      ])
      ->update();
    $this->table("adventure_npcs")
      ->insert([
        [
          "id" => 1, "name" => "Zloděj", "adventure" => 1, "order" => 1, "hitpoints" => 20, "strength" => 1, "armor" => 0, "reward" => 4, "encounter_text" => "Po několika okamžicích spatříš podezřele vypadajícího muže. Ten tě také vidí a vrhá se na tebe s nepřátelským pohledem.", "victory_text" => "Jakmile jej přemůžeš, přiznává se ti, že to on odváděl ovce svých sousedů, aby je tu prodal nějakému obchodníkovi. Prý přišel při poslední povodni o vše. Rozhodl jsi se jej předvést před jeho pána, ať rozhodne o jeho osudu.",
        ],
        [
          "id" => 2, "name" => "Bandita", "adventure" => 2, "order" => 1, "hitpoints" => 15, "strength" => 1, "armor" => 0, "reward" => 3, "encounter_text" => "Rozhlížíš a málem si nevšimneš, že na tebe útočí bandita. V poslední chvíli uhneš a tasíš zbraň.", "victory_text" => "Když porazíš banditu, snažíš se z něj dostat nějaké informace, které by ti pomohly najít unesenou, ale nepodaří se ti z něj dostat více než, že jsi udělal velkou chybu a že ostatní tě dostanou. Nezbývá ti tedy nic jiného, než to tu důkladně prohledat.",
        ],
        [
          "id" => 3, "name" => "Bandita", "adventure" => 2, "order" => 2, "hitpoints" => 20, "strength" => 2, "armor" => 0, "reward" => 5, "encounter_text" => "Bohužel v nejbližším okolí vidíš jen keře a kamení. Z jednoho keře vyskočí bandita a vrhá se na tebe. Snad budeš mít tentokrát větší štěstí.", "victory_text" => "Jakmile se bandita vzdá, chystáš se jej vyslechnout, když v tom ...",
        ],
        [
          "id" => 4, "name" => "Silný bandita", "adventure" => 2, "order" => 3, "hitpoints" => 24, "strength" => 3, "armor" => 1, "reward" => 6, "encounter_text" => "se, nevíš odkud, vynoří další bandita a vypadá, že se tě chystá rozčtvrtit.", "victory_text" => "Po menším boji nakonec porazíš i tohoto banditu a začneš s výslechem. Oba bandité jsou tak vyděšení z tvé síly, že ti okamžitě prozradí, že jejich zajatec je u jejich šéfa. Bez jakéhokoliv mučení ti také prozradí, kde je jejich šéf. Vydáváš se tedy za ním.",
        ],
        [
          "id" => 5, "name" => "Šéf banditů", "adventure" => 2, "order" => 4, "hitpoints" => 30, "strength" => 3, "armor" => 1, "reward" => 7, "encounter_text" => "Bez jakéhokoliv problému najdeš šéfa banditů. Ten se, narozdíl od svých druhů, na tebe bezhlavě nevrhne, když tě spatří, místo toho se tě ptá, co tu pohledáváš. Řekneš mu, že jsi přišel zachránit rychtářovu dceru. On ti potvrdí, že ji nedávno unesli a také ti řekne, že si je na to najal nějaký nepřítel jejího otce. Nakonec ti klidný hlasem poví, že ty už se odtud nedostaneš. Dokaž mu, že se mýlí!", "victory_text" => "Po tuhém boji nakonec šéf banditů padá vyčerpáním k boji. Ve stejném okamžiku vylézá z nedalekého keře asi sedmnáctiletá dívka, která odpovídá popisu rychtářovy dcery. Překotně ti děkuje za svou záchranu a dokonce tě políbí na tvář. Nakonec usednete na tvého věrného oře a vydáváte se zpět do jejího města.",
        ],
        [
          "id" => 6, "name" => "Lupič", "adventure" => 3, "order" => 1, "hitpoints" => 22, "strength" => 2, "armor" => 1, "reward" => 5, "encounter_text" => "Ten nakonec přijede a má plánovanou nehodu. Vidíš z lesa přicházet menší skupinku lupičů. Jdou k přímo k vozu, tebe si nevšimnou. Když dojdou, smějí se nehodě a chystají se otevřít vůz, když je zastavíš. Ten nejsilnější se na tebe vrhá.", "victory_text" => "Po vítězství nemáš ani možnost si oddechnout, protože se na okamžitě řítí další.",
        ],
        [
          "id" => 7, "name" => "Lupič", "adventure" => 3, "order" => 2, "hitpoints" => 24, "strength" => 2, "armor" => 1, "reward" => 5, "encounter_text" => "Hodí po tobě nějaké malé kovové kolo s hroty. Jsi překvapen, ale v poslední chvíli se vzpamatuješ a uhneš. To jej ale neodradilo a útočí na tebe šavlí. Braň se.", "victory_text" => "Lupič padá vyčerpaný k zemi a ostatní se snaží uprchnout. Podaří se ti je všechny zastavit a začneš jim pokládat otázky. Kolik jich je, kde mají základnu ... Trochu se ošívají, ale nakonec ti vše prozradí. Jak jsi předpokládal, sídlí v blízké lese. Vydáváš se tam tedy.",
        ],
        [
          "id" => 8, "name" => "Jednooký lupič", "adventure" => 3, "order" => 3, "hitpoints" => 27, "strength" => 3, "armor" => 1, "reward" => 6, "encounter_text" => "Prohlížíš si les. Je v něm mnoho stromů, lupiči se tu mohou dobře skrývat. Musíš být tedy opatrný. Po chvíli slyšíš šustnutí a současně vylézá ze svého úkrytu lupič s páskou přes oko. Jde přímo k tobě. Jeho chůze je pomalá, klidná. Kdyby nevytahoval meč, myslel by sis, že jen prochází kolem. Když je asi metr od tebe, vydá hrozivý zvuk a vrhá se na tebe.", "victory_text" => "Bojoval dobře, ale také padá k zemi. Pokračuješ k základně bandy.",
        ],
        [
          "id" => 9, "name" => "Jednooký lupič", "adventure" => 3, "order" => 4, "hitpoints" => 32, "strength" => 3, "armor" => 0, "reward" => 7, "encounter_text" => "Nejdeš ani pět minut a útočí na tebe další lupič, tentokrát mu chybí jedna ruka. To bude krátký boj, říkáš si.", "victory_text" => "Také byl, ale ve chvíli, kdy je porazíš, k vám přichází skupina 7 po zuby ozbrojený lupičů. Ty neporazíš. Vzdáváš se, oni tě svážou a odvádějí tě do své základny.",
        ],
        [
          "id" => 10, "name" => "Šéf lupičů", "adventure" => 3, "order" => 5, "hitpoints" => 40, "strength" => 3, "armor" => 3, "reward" => 10, "encounter_text" => "Jakmile dojdete, shodí tě na zem a kopou do tebe. Po chvíli je to ale přestane bavit, tak přestanou a rozcházejí se. Tři si jdou lehnout k ohništi, dva odejdou za jinými úkoly. Zůstává u tebe jen jejich šéf a jeden další lupič. Vyčkáváš na příležitost k útěku. Ta nastane o půl hodiny později, když lupiči u ohniště konečně usnou a další lupič odchází. Ty nenápadně rozvážeš provazy a vezmeš si svou zbraň. V tu chvíli si tě ale všimne šéf a zaútočí na tebe.", "victory_text" => "Po tuhém boji jej porazíš a on padá. Místo aby spadl na zem však dopadá na nějakou tyč, které jej probodne. Jeho křik probouzí spáče u ohniště. Ty ale nečekáš, až k tobě přijdou a utíkáš pryč z lesa.",
        ],
        [
          "id" => 11, "name" => "Opilec", "adventure" => 4, "order" => 1, "hitpoints" => 25, "strength" => 3, "armor" => 1, "reward" => 2, "encounter_text" => "Cesta ti velmi rychle uběhla, ani jsi nenarazil na žádné lapky. Po příjezdu okamžitě zamíříš do místního hostince se posilnit a taky získat nějaké informace o lupičích. Žádný stůl však není prázdný a ty si musíš sednout k muži, který silně zapáchá. Nejen potem ale i alkoholem. Konečně ti obsluha donese objednané jídlo a ty se do něj pustíš. Po chvíli však tvůj spolustolovník na tebe začne bezdůvodně řvát a dokonce ti dá facku.", "victory_text" => "Stačilo mu několik ran a hned padal k zemi. Ty si poté s klidem opět sedneš a věnuješ se svému jídlu. Po chvíli si k tobě přisedne mladá a nezapáchající žena. Obdivuje se tvé síle a když se jí svěříš se svou misí, ráda ti poradí, kde cech sídlí. Prý je to v podzemí, kde si postavili síť kanálů a dokonce i několik příbytků. Nezní to příliš hezky, ale práce je práce a tak se tam vydáš.",
        ],
        [
          "id" => 12, "name" => "Hlídač", "adventure" => 4, "order" => 2, "hitpoints" => 27, "strength" => 3, "armor" => 2, "reward" => 4, "encounter_text" => "Nakonec dojdeš ke vstupu, o kterém ti řekla ta žena. Avšak ve chvíli, kdy chceš vstoupit, na tebe vyskočí, ani nevíš odkud, hlídač.", "victory_text" => "Po chvíli ale už leží na zemi a vykládá ti o tom, že vůbec nevíš, do čeho se pouštíš a že se odtud nedostaneš. Ty jej ignoruješ a vstupuješ do podzemí. Je tu však spousta cest a ty nevíš, kterou se vydat. Následuješ tedy svůj instinkt a jednu zvolíš. Snad je to ta správná.",
        ],
        [
          "id" => 13, "name" => "Hlídač", "adventure" => 4, "order" => 3, "hitpoints" => 30, "strength" => 3, "armor" => 2, "reward" => 5, "encounter_text" => "Po několika minutách si zjistíš, že tato ulička je slepá. Chystáš se tedy vrátit a vybrat si jinou cestu. Vtom ti ale zastoupí cestu hlídač.", "victory_text" => "Nebyl ale příliš silný, proto jej rychle porazíš a jdeš dál. Tentokrát už v klidu dojdeš ke křižovatce a vybíráš si jinou chodbu. Doufáš, že tentokrát už dojdeš ke svému cíli.",
        ],
        [
          "id" => 14, "name" => "Zloděj", "adventure" => 4, "order" => 4, "hitpoints" => 30, "strength" => 3, "armor" => 3, "reward" => 6, "encounter_text" => "Zdá se, že nyní byla tvá volba lepší. Cesta tě dovede až k menšímu domku. Vypadá zchátrale a nejsou u něj žádné stráže. Rozhodneš se jej prozkoumat. Avšak jen co vejdeš dovnitř, začne ze stropu padat hromada dýk. Jen taktak se ti podaří uhnout. Nedostaneš ani čas se vzpamatovat, protože téměř okamžitě se na tebe vrhá zloděj.", "victory_text" => "Rychle jej zneškodníš a začneš s jeho výslechem. Začne s obvyklými řečmi, že děláš velkou chybu, ale nakonec z něj dostaneš i, že opravdu okradli místního pána a že lup je prozatím zde. Po prozrazení této informace však omdlí.",
        ],
        [
          "id" => 15, "name" => "Zlodějka", "adventure" => 4, "order" => 5, "hitpoints" => 33, "strength" => 3, "armor" => 3, "reward" => 7, "encounter_text" => "Neztrácíš tedy čas a začneš s průzkumem domu. Zdá se, že slouží zlodějům jako skladiště, protože se zde válí spousta balíčků a dokonce i rozbitých věcí. Po chvílí slyšíš, jak na tebe letí nůž. Soustředíš se a chytíš jej. Zaraduješ se, ale o vteřinu později už u tebe stojí zlodějka a útočí na tebe.", "victory_text" => "Po několika minutách boje se ale zhroutí na zem a vzdává se ti. Ty skončíš s bojem a začneš jí pokládat otázky. Kde se nachází lup, který hledáš, kdo jej hlídá atd. Ona ti, s přerývaných dechem, odpoví, že to hledáš, chrání osobně jejich vůdce, kterého ale jistě neporazíš. A dodává, že i kdyby se ti to nějakým zázrakem podařilo, živý se odtud nedostaneš. Ty se nenecháš rozhodit a vydáš se za vůdcem zlodějů.",
        ],
        [
          "id" => 16, "name" => "Vůdce zlodějů", "adventure" => 4, "order" => 6, "hitpoints" => 37, "strength" => 4, "armor" => 4, "reward" => 12, "encounter_text" => "Nemáš vůbec problém jej najít a nikdo ti ani nestojí v cestě. Začínáš tušit, že na slovech zlodějky asi něco bude. Nemáš čas ale příliš přemýšlet, protože vůdce zlodějů si tě všimne a míří k tobě. Během jeho chůze si jej prohlížíš. Je štíhlý a mrštný. Nebude lehkým soupeřem. Než si ale stihneš promyslet svou strategii, už na tebe útočí.", "victory_text" => "Po dlouhém boji nakonec pořádně udeříš a tvůj protivník se začne svíjet bolestí. Ptáš se jej, kde jsou věci, které ukradl místnímu pánovi. On ukáže na jednu hromádku poblíž. Prohlížíš si pozorně dané věci a shledáš, že mluví pravdu. Pobereš tedy dané věci a chystáš se k odchodu. V tu chvíli ale ze všech koutů vyleze asi deset zlodějů s hrozivým pohledem v očích. Lekneš se a začneš rychle přemýšlet jak se dostat ven. Ke dveřím se nestihneš dostat včas a okna žádná nevidíš. Z přemýšlení tě vyruší hluk u dveří.",
        ],
      ])
      ->update();
    $this->table("shops")
      ->insert([
        [
          "id" => 1, "name" => "Hamalův všehoobchod", "description" => "popisek",
        ],
        [
          "id" => 2, "name" => "Jackovo klenotnictví", "description" => "Jen se podívejte na mou bohatou nabídku šperků.",
        ],
        [
          "id" => 3, "name" => "Zbyslavovy zbraně", "description" => "Jen pojďte dál, mám ty nejlepší zbraně v celé Nexendrii!",
        ],
        [
          "id" => 4, "name" => "Kazimířin magický obchůdek", "description" => "Mám vše, co čaroděj potřebuje!",
        ],
        [
          "id" => 5, "name" => "Meklavovy lektvary", "description" => "Chystáte se na dobrodružství a potřebujete lektvary? Jste na správné místě!",
        ],
        [
          "id" => 6, "name" => "Vaškovy zbroje", "description" => "Dobrá zbroj je důležitá pro vítězství v souboji.",
        ],
      ])
      ->update();
    $this->table("items")
      ->insert([
        [
          'id' => 1,
          'name' => 'Jednoduchý náramek',
          'description' => 'Jednoduchý kamenný náramek',
          'price' => 4,
          'shop' => 2,
          'type' => 'item',
          'strength' => 0,
        ],
        [
          'id' => 2,
          'name' => 'Měšec',
          'description' => 'Obyčejný měšec, vleze se do něj sotva 20 mincí',
          'price' => 16,
          'shop' => 1,
          'type' => 'item',
          'strength' => 0,
        ],
        [
          'id' => 3,
          'name' => 'Dřevěný meč',
          'description' => 'Obyčejný dřevěný meč',
          'price' => 5,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 1,
        ],
        [
          'id' => 4,
          'name' => 'Železný meč',
          'description' => 'Obyčejný meč ze železa',
          'price' => 21,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 2,
        ],
        [
          'id' => 5,
          'name' => 'Košile',
          'description' => 'Čistá bílá košile. Nosí se při slavnostních příležitostech nebo v krajní nouzi i v boji.',
          'price' => 10,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 1,
        ],
        [
          'id' => 6,
          'name' => 'Stříbrný meč',
          'description' => 'Meč ze zvláštní stříbrné slitiny',
          'price' => 52,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 3,
        ],
        [
          'id' => 7,
          'name' => 'Zlatý meč',
          'description' => 'Meč z kvalitního zlata.',
          'price' => 86,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 4,
        ],
        [
          'id' => 8,
          'name' => 'Sekyrka',
          'description' => 'Nejobyčejnější sekera',
          'price' => 21,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 2,
        ],
        [
          'id' => 9,
          'name' => 'Válečná sekera',
          'description' => 'Kvalitní dvoubřitvá sekera',
          'price' => 126,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 5,
        ],
        [
          'id' => 10,
          'name' => 'Vycpávaná zbroj',
          'description' => 'Dvouvrstvá kožená zbroj',
          'price' => 17,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 2,
        ],
        [
          'id' => 11,
          'name' => 'Kroužková košile',
          'description' => 'Kvalitní kroužkové brnění',
          'price' => 48,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 3,
        ],
        [
          'id' => 12,
          'name' => 'Kyrys',
          'description' => 'Kvalitní několikavrstvé plátové brnění',
          'price' => 91,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 4,
        ],
        [
          'id' => 13,
          'name' => 'Pozlacený kyrys',
          'description' => 'Artefaktový kyrys vyrobený kovářským mistrem a očarovaný mocným čarodějem',
          'price' => 153,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 5,
        ],
        [
          'id' => 14,
          'name' => 'Slabý elixír zdraví',
          'description' => 'Nejslabší elixír. Obnoví 2 životy',
          'price' => 8,
          'shop' => 5,
          'type' => 'potion',
          'strength' => 2,
        ],
        [
          'id' => 15,
          'name' => 'Právo na založení města',
          'description' => 'Dokument vydaný královnou opravňující k založení města či vesnice',
          'price' => 999,
          'shop' => null,
          'type' => 'charter',
          'strength' => 0,
        ],
        [
          'id' => 16,
          'name' => 'Střední elixír zdraví',
          'description' => 'Silnější elixír, který obnoví 5 životů',
          'price' => 23,
          'shop' => 5,
          'type' => 'potion',
          'strength' => 5,
        ],
        [
          'id' => 17,
          'name' => 'Velký elixír zdraví',
          'description' => 'Silný elixír, který obnoví 7 životů',
          'price' => 33,
          'shop' => 5,
          'type' => 'potion',
          'strength' => 7,
        ],
        [
          'id' => 18,
          'name' => 'Kožená přilba',
          'description' => 'Nejjednodušší přilba, poskytuje jen slabou ochranu',
          'price' => 25,
          'shop' => 6,
          'type' => 'helmet',
          'strength' => 1,
        ],
        [
          'id' => 19,
          'name' => 'Kopí',
          'description' => 'Obyčejné kopí',
          'price' => 5,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 1,
        ],
        [
          'id' => 20,
          'name' => 'Pilum',
          'description' => 'Krátké, avšak nebezpečné kopí',
          'price' => 52,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 3,
        ],
        [
          'id' => 21,
          'name' => 'Sudlice',
          'description' => 'Silná dřevcová zbraň',
          'price' => 84,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 4,
        ],
        [
          'id' => 22,
          'name' => 'Kropáč',
          'description' => 'Dlouhá palice s ostny',
          'price' => 126,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 5,
        ],
        [
          'id' => 23,
          'name' => 'Bojový nůž',
          'description' => 'Krátká bodná a řezná zbraň',
          'price' => 5,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 1,
        ],
        [
          'id' => 24,
          'name' => 'Platinový meč',
          'description' => 'Velmi kvalitní dlouhý meč',
          'price' => 126,
          'shop' => 3,
          'type' => 'weapon',
          'strength' => 5,
        ],
        [
          'id' => 25,
          'name' => 'Ozdobný měšec',
          'description' => 'Měšec s jednoduchou kresbou, pojme asi 50 mincí',
          'price' => 35,
          'shop' => 1,
          'type' => 'item',
          'strength' => 1,
        ],
        [
          'id' => 26,
          'name' => 'Vandrákův plášť',
          'description' => 'Potrhaný, lehký, starý plášť. Poskytuje velmi slabou ochranu',
          'price' => 17,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 1,
        ],
        [
          'id' => 27,
          'name' => 'Kápě učedníka',
          'description' => 'Obyčejné černé roucho používané čaroději - učedníky',
          'price' => 15,
          'shop' => 4,
          'type' => 'armor',
          'strength' => 1,
        ],
        [
          'id' => 28,
          'name' => 'Plášť zloděje',
          'description' => 'Lehký, černý plášť, který neomezuje pohyb a zároveň poskytuje ochranu',
          'price' => 36,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 2,
        ],
        [
          'id' => 29,
          'name' => 'Kápě mistra zloděje',
          'description' => 'Dlouhý, hebký, fialový plášť',
          'price' => 130,
          'shop' => 6,
          'type' => 'armor',
          'strength' => 3,
        ],
        [
          'id' => 30,
          'name' => 'Hůlka učedníka',
          'description' => 'Krátká hůlka z osikového dřeva pro učedníky',
          'price' => 13,
          'shop' => 4,
          'type' => 'weapon',
          'strength' => 1,
        ],
        [
          'id' => 31,
          'name' => 'Čepice učedníka',
          'description' => 'Malá, šedá, nepříliš pohodlná čepice',
          'price' => 36,
          'shop' => 4,
          'type' => 'helmet',
          'strength' => 1,
        ],
        [
          'id' => 32,
          'name' => 'Čapka',
          'description' => 'Stará čepice s několika dírkami',
          'price' => 35,
          'shop' => 6,
          'type' => 'helmet',
          'strength' => 1,
        ],
        [
          'id' => 33,
          'name' => 'Hůlka čaroděje',
          'description' => 'Hůlka z borovicového dřeva. Používají jej zkušenější čarodějové',
          'price' => 35,
          'shop' => 4,
          'type' => 'weapon',
          'strength' => 2,
        ],
        [
          'id' => 34,
          'name' => 'Hůl mistra',
          'description' => 'Dlouhá, pevná hůl z dubového dřeva',
          'price' => 79,
          'shop' => 4,
          'type' => 'weapon',
          'strength' => 3,
        ],
        [
          'id' => 35,
          'name' => 'Arcimágova hůl',
          'description' => 'Dlouhá bílá hůl vyrobená z mramoru',
          'price' => 148,
          'shop' => 4,
          'type' => 'weapon',
          'strength' => 4,
        ],
        [
          'id' => 36,
          'name' => 'Čarodějova kápě',
          'description' => 'Zelená, pohodlná kápě používaná čaroději',
          'price' => 26,
          'shop' => 4,
          'type' => 'armor',
          'strength' => 2,
        ],
        [
          'id' => 37,
          'name' => 'Kápě mistra čaroděje',
          'description' => 'Modrá kápě používaná mistry čaroději',
          'price' => 64,
          'shop' => 4,
          'type' => 'armor',
          'strength' => 3,
        ],
        [
          'id' => 38,
          'name' => 'Arcimágův kabát',
          'description' => 'Zdobený fialový kabát',
          'price' => 127,
          'shop' => 4,
          'type' => 'armor',
          'strength' => 4,
        ],
        [
          'id' => 39,
          'name' => 'Čarodějova čepice',
          'description' => 'Žlutá, pohodlná čepice používaná čaroději',
          'price' => 49,
          'shop' => 4,
          'type' => 'helmet',
          'strength' => 2,
        ],
        [
          'id' => 40,
          'name' => 'Klobouk mistra',
          'description' => 'Velký šedý klobouk používaný mistry čaroději',
          'price' => 73,
          'shop' => 4,
          'type' => 'helmet',
          'strength' => 3,
        ],
        [
          'id' => 41,
          'name' => 'Arcimágův klobouk',
          'description' => 'Zdobený černý klobouk',
          'price' => 103,
          'shop' => 4,
          'type' => 'helmet',
          'strength' => 4,
        ],
        [
          'id' => 42,
          'name' => 'Malé srdce',
          'description' => 'Zvýší důvěrnost manželů o 1',
          'price' => 17,
          'shop' => null,
          'type' => 'intimacy_boost',
          'strength' => 1,
        ],
        [
          'id' => 43,
          'name' => 'Střední srdce',
          'description' => 'Zvýší důvěrnost manželů o 2',
          'price' => 28,
          'shop' => null,
          'type' => 'intimacy_boost',
          'strength' => 2,
        ],
        [
          'id' => 44,
          'name' => 'Velké srdce',
          'description' => 'Zvýší důvěrnost manželů o 5',
          'price' => 83,
          'shop' => null,
          'type' => 'intimacy_boost',
          'strength' => 5,
        ],
      ])
      ->update();
    $this->table("item_sets")
      ->insert([
        [
          'id' => 1,
          'name' => 'Odvedencova sada',
          'weapon' => 3,
          'armor' => 5,
          'helmet' => 18,
          'stat' => 'hitpoints',
          'bonus' => 3,
        ],
        [
          'id' => 2,
          'name' => 'Sada mladého čaroděje',
          'weapon' => 30,
          'armor' => 27,
          'helmet' => 31,
          'stat' => 'damage',
          'bonus' => 2,
        ],
        [
          'id' => 3,
          'name' => 'Vandrákova sada',
          'weapon' => 23,
          'armor' => 26,
          'helmet' => 32,
          'stat' => 'armor',
          'bonus' => 2,
        ],
        [
          'id' => 4,
          'name' => 'Čarodějova sada',
          'weapon' => 33,
          'armor' => 36,
          'helmet' => 39,
          'stat' => 'damage',
          'bonus' => 4,
        ],
        [
          'id' => 5,
          'name' => 'Mistr čaroděj',
          'weapon' => 34,
          'armor' => 37,
          'helmet' => 40,
          'stat' => 'damage',
          'bonus' => 6,
        ],
        [
          'id' => 6,
          'name' => 'Arcimágova sada',
          'weapon' => 35,
          'armor' => 38,
          'helmet' => 41,
          'stat' => 'damage',
          'bonus' => 8,
        ],
      ])
      ->update();
    $this->table("skills")
      ->insert([
        [
          "id" => 1, "name" => "Rybolov", "price" => 15, "max_level" => 5, "type" => "work", "stat" => null, "stat_increase" => 0,
        ],
        [
          "id" => 2, "name" => "Péče o zvířata", "price" => 15, "max_level" => 5, "type" => "work", "stat" => null, "stat_increase" => 0,
        ],
        [
          "id" => 3, "name" => "Čtení a psaní", "price" => 35, "max_level" => 5, "type" => "work", "stat" => null, "stat_increase" => 0,
        ],
        [
          "id" => 4, "name" => "Těžba", "price" => 15, "max_level" => 5, "type" => "work", "stat" => null, "stat_increase" => 0,
        ],
        [
          "id" => 5, "name" => "Zacházení se zbraněmi", "price" => 30, "max_level" => 5, "type" => "work", "stat" => null, "stat_increase" => 0,
        ],
        [
          "id" => 6, "name" => "Obchodování", "price" => 35, "max_level" => 5, "type" => "work", "stat" => null, "stat_increase" => 0,
        ],
        [
          "id" => 7, "name" => "Výdrž", "price" => 50, "max_level" => 10, "type" => "combat", "stat" => "hitpoints", "stat_increase" => 5,
        ],
        [
          "id" => 8, "name" => "Houževnatost", "price" => 50, "max_level" => 5, "type" => "combat", "stat" => "armor", "stat_increase" => 1,
        ],
        [
          "id" => 9, "name" => "Síla", "price" => 50, "max_level" => 5, "type" => "combat", "stat" => "damage", "stat_increase" => 1,
        ],
        [
          "id" => 10, "name" => "Vaření", "price" => 20, "max_level" => 5, "type" => "work", "stat" => null, "stat_increase" => 0,
        ],
      ])
      ->update();
    $this->table("jobs")
      ->insert([
        [
          'id' => 1,
          'name' => 'Rybář',
          'description' => 'Rybář loví v řece ryby a následně je prodává.',
          'help' => 'Lovit můžeš každých 70 minut a za 1 ulovenou rybu dostaneš %reward%. Pamatuj ale, že lov se nemusí vždy podařit!',
          'count' => 0,
          'award' => 2,
          'shift' => 70,
          'level' => 50,
          'needed_skill' => 1,
          'needed_skill_level' => 0,
        ],
        [
          'id' => 2,
          'name' => 'Horník',
          'description' => 'Horník v podzemí těží nerosty.',
          'help' => 'Běž hledat nerosty. Musíš jich najít aspoň %count%, abys dostal %reward%. Jestli to nezvládneš, tak si mě nepřej!',
          'count' => 20,
          'award' => 80,
          'shift' => 50,
          'level' => 50,
          'needed_skill' => 4,
          'needed_skill_level' => 1,
        ],
        [
          'id' => 3,
          'name' => 'Pastýř',
          'description' => 'Pastýř se stará o zvířata.',
          'help' => 'Postarej se o tohle zvíře na 2 hodiny. Pokud se alespoň %count% nic nestane, dostaneš %reward%.',
          'count' => 13,
          'award' => 70,
          'shift' => 120,
          'level' => 50,
          'needed_skill' => 2,
          'needed_skill_level' => 0,
        ],
        [
          'id' => 4,
          'name' => 'Strážník',
          'description' => 'Strážník dohlíží na klid a pořádek v ulicích města.',
          'help' => 'Vydej se hlídat ulice města. Za každou hodinu, kdy se nic nestane, dostaneš %reward%.',
          'count' => 0,
          'award' => 4,
          'shift' => 60,
          'level' => 100,
          'needed_skill' => 5,
          'needed_skill_level' => 1,
        ],
        [
          'id' => 5,
          'name' => 'Písař',
          'description' => 'Písař píše zápisy a listiny. Také přepisuje knihy.',
          'help' => 'Tak už se dej do psaní. Musíš zvládnout alespoň %count% směn po hodině a půl, aby sis zasloužil %reward%. Jestli tam ale bude mnoho chyb, tak ti to strhnu!',
          'count' => 15,
          'award' => 180,
          'shift' => 90,
          'level' => 100,
          'needed_skill' => 3,
          'needed_skill_level' => 1,
        ],
        [
          'id' => 6,
          'name' => 'Kupec',
          'description' => 'Kupec nakupuje zboží a následně jej jinde za vyšší cenu prodává.',
          'help' => 'Nakup nějaké zboží a pokus se jej prodat v sousedním městě.',
          'count' => 0,
          'award' => 8,
          'shift' => 120,
          'level' => 100,
          'needed_skill' => 6,
          'needed_skill_level' => 1,
        ],
        [
          'id' => 7,
          'name' => 'Bankovní úředník',
          'description' => 'Banka Žajských potřebuje posily do místní pobočky. Nejvíce jsou potřeba úředníci na příjem a výdej hotovosti. Požadují se zkušenosti s obchodní činností.',
          'help' => 'Musíš obsluhovat klienty přicházející do banky. Aby sis zasloužil %reward%, musíš zvládnout alespoň %count% hodin.',
          'count' => 20,
          'award' => 220,
          'shift' => 60,
          'level' => 100,
          'needed_skill' => 6,
          'needed_skill_level' => 2,
        ],
        [
          'id' => 8,
          'name' => 'Žoldnéř',
          'description' => 'Pohraniční oblasti byly napadeny našimi sousedy a místní páni nemají dostatek mužů, aby odrazili nepřátele. Slibují odměnu za pomoc.',
          'help' => 'Braň vesnici proti útokům cizáků.',
          'count' => 0,
          'award' => 10,
          'shift' => 90,
          'level' => 400,
          'needed_skill' => 5,
          'needed_skill_level' => 2,
        ],
        [
          'id' => 9,
          'name' => 'Pomocník v kuchyni',
          'description' => 'Místní pán má nedostatek lidí v kuchyni. Budeš jen nosit či upravovat jednotlivé suroviny.',
          'help' => 'Přines to a tamto. A pospěš si nebo nedostaneš %reward% za tuhle hodinu!',
          'count' => 0,
          'award' => 3,
          'shift' => 60,
          'level' => 50,
          'needed_skill' => 10,
          'needed_skill_level' => 0,
        ],
        [
          'id' => 10,
          'name' => 'Kuchař',
          'description' => 'Jeden z kuchařů místního pána je nemocný. Dokážeš jej zastoupit?',
          'help' => 'Pán chce k večeři pečené holuby. Nekoukej tak a dej se do práce!',
          'count' => 12,
          'award' => 110,
          'shift' => 60,
          'level' => 90,
          'needed_skill' => 10,
          'needed_skill_level' => 1,
        ],
        [
          'id' => 11,
          'name' => 'Tělesný strážce',
          'description' => 'Významný obchodník potřebuje ochranu během obchodní cesty.',
          'help' => 'Braň obchodníka!',
          'count' => 20,
          'award' => 250,
          'shift' => 45,
          'level' => 400,
          'needed_skill' => 5,
          'needed_skill_level' => 3,
        ],
        [
          'id' => 12,
          'name' => 'Pekař',
          'description' => 'Pekař peče a prodává pečivo.',
          'help' => 'Dej se do pečení. Za každý kus dostaneš %reward%.',
          'count' => 0,
          'award' => 6,
          'shift' => 90,
          'level' => 100,
          'needed_skill' => 10,
          'needed_skill_level' => 2,
        ],
        [
          'id' => 13,
          'name' => 'Knihovník',
          'description' => 'V místní akademie je neobvykle mnoho studentů, kteří dychtí po nových znalostech. Sežeň jim potřebné studijní materiály z archivu a postarej se o nezbytnou administrativu.',
          'help' => 'Přichází další student. Běž mu pomoct.',
          'count' => 20,
          'award' => 200,
          'shift' => 45,
          'level' => 100,
          'needed_skill' => 3,
          'needed_skill_level' => 2,
        ],
      ])
      ->update();
    $this->table("job_messages")
      ->insert([
        ['id' => 1, 'job' => 1, 'success' => true, 'message' => 'Ulovil(a) jsi a prodal(a) 1 rybu.'],
        ['id' => 2, 'job' => 1, 'success' => false, 'message' => 'Nepodařilo se ti nic ulovit.'],
        ['id' => 3, 'job' => 1, 'success' => false, 'message' => 'Někdo ti ukradl chycenou rybu.'],
        ['id' => 4, 'job' => 2, 'success' => true, 'message' => 'Vytěžil(a) jsi 1 nerost.'],
        ['id' => 5, 'job' => 2, 'success' => false, 'message' => 'Nic jsi nenašel.'],
        ['id' => 6, 'job' => 3, 'success' => true, 'message' => 'Svěřené ovci se nic nestalo.'],
        ['id' => 7, 'job' => 3, 'success' => true, 'message' => 'Svěřené krávě se nic nestalo.'],
        ['id' => 8, 'job' => 3, 'success' => false, 'message' => 'Svěřená ovce se zaběhla.'],
        ['id' => 9, 'job' => 3, 'success' => false, 'message' => 'Svěřená kráva se zaběhla.'],
        ['id' => 10, 'job' => 4, 'success' => true, 'message' => 'Během tvé služby se nic nestalo.'],
        ['id' => 11, 'job' => 4, 'success' => true, 'message' => 'Chytil(a) jsi 1 zloděje.'],
        ['id' => 12, 'job' => 4, 'success' => false, 'message' => 'Unikl ti zloděj.'],
        ['id' => 13, 'job' => 4, 'success' => false, 'message' => 'Zranili tě protestující občaně.'],
        [
          'id' => 14,
          'job' => 5,
          'success' => true,
          'message' => 'Napsal(a) jsi zápis bez jedniné chyby.',
        ],
        ['id' => 15, 'job' => 5, 'success' => true, 'message' => 'Přepsal(a) jsi 1 kapitolu knihy.'],
        ['id' => 16, 'job' => 5, 'success' => true, 'message' => 'Napsal(a) jsi zápis z městské rady.'],
        ['id' => 17, 'job' => 5, 'success' => false, 'message' => 'Rozlil se ti inkoust.'],
        ['id' => 18, 'job' => 5, 'success' => false, 'message' => 'Udělal(a) jsi v zápisu spostu chyb.'],
        ['id' => 19, 'job' => 2, 'success' => false, 'message' => 'Praštil(a) jsi se do hlavy.'],
        ['id' => 20, 'job' => 4, 'success' => true, 'message' => 'Uklidnil(a) jsi rozzuřený dav.'],
        ['id' => 21, 'job' => 6, 'success' => true, 'message' => 'Podařilo se ti prodat zboží.'],
        [
          'id' => 22,
          'job' => 6,
          'success' => false,
          'message' => 'Na cestě do sousedního města tě přepadli lupiči.',
        ],
        ['id' => 23, 'job' => 6, 'success' => false, 'message' => 'Tvé zboží nechtěl nikdo koupit.'],
        ['id' => 24, 'job' => 9, 'success' => false, 'message' => 'Přinesl(a) jsi špatnou surovinu.'],
        ['id' => 25, 'job' => 9, 'success' => false, 'message' => 'Poranil(a) jsi se při krájení.'],
        [
          'id' => 26,
          'job' => 11,
          'success' => true,
          'message' => 'Ochránil(a) jsi obchodníka před lupiči.',
        ],
        [
          'id' => 27,
          'job' => 11,
          'success' => true,
          'message' => 'Prošli jste bez obtíží tímto úsekem.',
        ],
        ['id' => 28, 'job' => 11, 'success' => false, 'message' => 'Lupiči ukradli část zboží.'],
        [
          'id' => 29,
          'job' => 11,
          'success' => false,
          'message' => 'Při boji jsi poškodil(a) část zboží.',
        ],
        [
          'id' => 30,
          'job' => 12,
          'success' => true,
          'message' => 'Upekl(a) a prodal(a) jsi 1 bochník chleba.',
        ],
        ['id' => 31, 'job' => 12, 'success' => true, 'message' => 'Upekl(a) a prodal(a) jsi 1 koblihu.'],
        ['id' => 32, 'job' => 12, 'success' => true, 'message' => 'Upekl(a) a prodal(a) jsi 1 koláč.'],
        [
          'id' => 33,
          'job' => 12,
          'success' => true,
          'message' => 'Upekl(a) a prodal(a) jsi 1 makový závin.',
        ],
        ['id' => 34, 'job' => 12, 'success' => false, 'message' => 'Tvé pečivo nikdo nechtěl koupit.'],
        ['id' => 35, 'job' => 12, 'success' => false, 'message' => 'Tvé pečivo nebylo poživatelné.'],
        ['id' => 36, 'job' => 13, 'success' => false, 'message' => 'Přinesl(a) jsi špatnou knihu.'],
        [
          'id' => 37,
          'job' => 13,
          'success' => false,
          'message' => 'Hledal(a) jsi knihu příliš dlouho, student odešel.',
        ],
      ])
      ->update();
    $this->table("meals")
      ->insert([
        [
          "id" => 1, "name" => "Placka", "message" => "Tady to je. Nesnáším výrobu ovesných placek, ale za těch 5 grošů to udělám.", "price" => 5, "life" => 2,
        ],
        [
          "id" => 2, "name" => "Ovesná kaše", "message" => "Už se to nese. Ovesná kaše!", "price" => 7, "life" => 2,
        ],
        [
          "id" => 3, "name" => "Pstruh", "message" => "Právě upečený pstruh s bramborama. Nech si chutnat.", "price" => 14, "life" => 3,
        ],
        [
          "id" => 4, "name" => "Chléb", "message" => "Tu máš bochník chleba.", "price" => 5, "life" => 2,
        ],
        [
          "id" => 5, "name" => "Voda", "message" => "Tady máš sklenici vody. Nic zvláštního? Ano, ale co bys čekal za 2 groše!", "price" => 2, "life" => 1,
        ],
      ])
      ->update();
    $this->table("mount_types")
      ->insert([
        [
          "id" => 1, "name" => "Osel", "female_name" => "Oslice", "young_name" => "Oslátko", "description" => ".", "level" => 50, "damage" => 0, "armor" => 0, "price" => 50,
        ],
        [
          "id" => 2, "name" => "Kůň", "female_name" => "Klisna", "young_name" => "Hříbě", "description" => ".", "level" => 100, "damage" => 0, "armor" => 1, "price" => 100,
        ],
        [
          "id" => 3, "name" => "Velbloud", "female_name" => "Velbloudice", "young_name" => "Velbloudě", "description" => ".", "level" => 100, "damage" => 1, "armor" => 0, "price" => 300,
        ],
        [
          "id" => 4, "name" => "Jednorožec", "female_name" => "Jednorožčice", "young_name" => "Jednorožče", "description" => ".", "level" => 400, "damage" => 1, "armor" => 2, "price" => 1200,
        ],
        [
          "id" => 5, "name" => "Drak", "female_name" => "Dráče", "young_name" => "Dráče", "description" => ".", "level" => 600, "damage" => 3, "armor" => 2, "price" => 5000,
        ],
      ])
      ->update();
    $this->table("guild_ranks")
      ->insert([
        [
          "id" => 1, "name" => "učedník", "income_bonus" => 5, "guild_fee" => 50,
        ],
        [
          "id" => 2, "name" => "tovaryš", "income_bonus" => 5, "guild_fee" => 75,
        ],
        [
          "id" => 3, "name" => "mistr", "income_bonus" => 10, "guild_fee" => 110,
        ],
        [
          "id" => 4, "name" => "cechmistr", "income_bonus" => 10, "guild_fee" => 135,
        ],
      ])
      ->update();
    $this->table("order_ranks")
      ->insert([
        [
          "id" => 1, "name" => "zbrojnoš", "adventure_bonus" => 5, "order_fee" => 65,
        ],
        [
          "id" => 2, "name" => "rytíř", "adventure_bonus" => 5, "order_fee" => 90,
        ],
        [
          "id" => 3, "name" => "mistr", "adventure_bonus" => 10, "order_fee" => 125,
        ],
        [
          "id" => 4, "name" => "velmistr", "adventure_bonus" => 10, "order_fee" => 150,
        ],
      ])
      ->update();
    $this->table("groups")
      ->insert([
        [
          "id" => 0, "name" => "Vládci", "single_name" => "král", "female_name" => "královna", "level" => 10001, "path" => "tower", "max_loan" => 2000,
        ],
        [
          "id" => 1, "name" => "Korunní rada", "single_name" => "kníže", "female_name" => "kněžna", "level" => 10000, "path" => "tower", "max_loan" => 2000,
        ],
        [
          "id" => 2, "name" => "Markrabata", "single_name" => "markrabě", "female_name" => "markraběnka", "level" => 1000, "path" => "tower", "max_loan" => 1500,
        ],
        [
          "id" => 3, "name" => "Panstvo", "single_name" => "lord", "female_name" => "lady", "level" => 600, "path" => "tower", "max_loan" => 1500,
        ],
        [
          "id" => 4, "name" => "Vyšší klérus", "single_name" => "velekněz", "female_name" => "velekněžka", "level" => 550, "path" => "church", "max_loan" => 1500,
        ],
        [
          "id" => 5, "name" => "Rytíři", "single_name" => "rytíř", "female_name" => "dáma", "level" => 400, "path" => "tower", "max_loan" => 700,
        ],
        [
          "id" => 6, "name" => "Duchovní", "single_name" => "kněz", "female_name" => "kněžka", "level" => 350, "path" => "church", "max_loan" => 500,
        ],
        [
          "id" => 7, "name" => "Rychtáři", "single_name" => "rychtář", "female_name" => "rychtářka", "level" => 345, "path" => "city", "max_loan" => 500,
        ],
        [
          "id" => 8, "name" => "Konšelé", "single_name" => "konšel", "female_name" => "konšelka", "level" => 300, "path" => "city", "max_loan" => 500,
        ],
        [
          "id" => 9, "name" => "Měšťané", "single_name" => "měšťan", "female_name" => "měšťanka", "level" => 100, "path" => "city", "max_loan" => 300,
        ],
        [
          "id" => 10, "name" => "Akolyté", "single_name" => "akolyta", "female_name" => "akolyta", "level" => 90, "path" => "church", "max_loan" => 300,
        ],
        [
          "id" => 11, "name" => "Mnišstvo", "single_name" => "bratr", "female_name" => "sestra", "level" => 55, "path" => "church", "max_loan" => 70,
        ],
        [
          "id" => 12, "name" => "Sedláci", "single_name" => "sedlák", "female_name" => "selka", "level" => 50, "path" => "city", "max_loan" => 70,
        ],
        [
          "id" => 13, "name" => "Cizinci", "single_name" => "cizinec", "female_name" => "cizinka", "level" => 0, "path" => "city", "max_loan" => 0,
        ],
        [
          "id" => 14, "name" => "Vězni", "single_name" => "vězeň", "female_name" => "vězeňkyně", "level" => 0, "path" => "city", "max_loan" => 0,
        ],
      ])
      ->update();
    $this->table("permissions")
      ->insert([
        [
          "id" => 1, "resource" => "site", "action" => "manage", "group" => 10,
        ],
        [
          "id" => 2, "resource" => "poll", "action" => "add", "group" => 1,
        ],
        [
          "id" => 3, "resource" => "poll", "action" => "vote", "group" => 12,
        ],
        [
          "id" => 4, "resource" => "article", "action" => "add", "group" => 10,
        ],
        [
          "id" => 5, "resource" => "article", "action" => "edit", "group" => 1,
        ],
        [
          "id" => 6, "resource" => "comment", "action" => "add", "group" => 12,
        ],
        [
          "id" => 7, "resource" => "comment", "action" => "delete", "group" => 1,
        ],
        [
          "id" => 8, "resource" => "group", "action" => "list", "group" => 1,
        ],
        [
          "id" => 9, "resource" => "group", "action" => "edit", "group" => 1,
        ],
        [
          "id" => 10, "resource" => "user", "action" => "list", "group" => 1,
        ],
        [
          "id" => 11, "resource" => "user", "action" => "edit", "group" => 1,
        ],
        [
          "id" => 12, "resource" => "user", "action" => "ban", "group" => 1,
        ],
        [
          "id" => 13, "resource" => "content", "action" => "list", "group" => 1,
        ],
        [
          "id" => 14, "resource" => "content", "action" => "add", "group" => 1,
        ],
        [
          "id" => 15, "resource" => "content", "action" => "edit", "group" => 1,
        ],
        [
          "id" => 16, "resource" => "site", "action" => "settings", "group" => 1,
        ],
        [
          "id" => 17, "resource" => "content", "action" => "gift", "group" => 1,
        ],
        [
          "id" => 18, "resource" => "event", "action" => "add", "group" => 1,
        ],
        [
          "id" => 19, "resource" => "event", "action" => "edit", "group" => 1,
        ],
        [
          "id" => 20, "resource" => "event", "action" => "delete", "group" => 1,
        ],
        [
          "id" => 21, "resource" => "content", "action" => "delete", "group" => 1,
        ],
        [
          "id" => 22, "resource" => "poll", "action" => "list", "group" => 1,
        ],
        [
          "id" => 23, "resource" => "event", "action" => "list", "group" => 1,
        ],
        [
          "id" => 24, "resource" => "town", "action" => "elect", "group" => 9,
        ],
        [
          "id" => 25, "resource" => "town", "action" => "manage", "group" => 1,
        ],
      ])
      ->update();
    $this->table("towns")
      ->insert([
        [
          "id" => 1, "name" => "Velehrad", "description" => "Starobylé hlavní město", "founded" => 1429779664, "owner" => 0, "price" => 5000, "on_market" => false,
        ],
        [
          "id" => 2, "name" => "Myhr", "description" => "Největší město na severovýchodě", "founded" => 1429779664, "owner" => 1, "price" => 5000, "on_market" => false,
        ],
        [
          "id" => 3, "name" => "Světlohvozd", "description" => "Malá vesnice ve středu země", "founded" => 1429779664, "owner" => 0, "price" => 5000, "on_market" => false,
        ],
        [
          "id" => 4, "name" => "Bělehrad", "description" => "Starobylé město na jihu země. Kdysi hlavní město (polo)samostatného knížectví, dnes královské a korunovační město", "founded" => 1429779664, "owner" => 0, "price" => 5000, "on_market" => false,
        ],
        [
          "id" => 5, "name" => "Lípa", "description" => "Vesnička na jihu země", "founded" => 1429779664, "owner" => 0, "price" => 5000, "on_market" => true,
        ],
        [
          "id" => 6, "name" => "Žalecký Brod", "description" => "Větší vesnice na východě země", "founded" => 1429779664, "owner" => 0, "price" => 5000, "on_market" => false,
        ],
        [
          "id" => 7, "name" => "Dazluk", "description" => "Přístavní město na západě", "founded" => 1429779664, "owner" => 0, "price" => 5000, "on_market" => true,
        ],
        [
          "id" => 8, "name" => "Bílé Louky", "description" => "Větší vesnice na jihu", "founded" => 1429779664, "owner" => 0, "price" => 5000, "on_market" => true,
        ],
        [
          "id" => 9, "name" => "Vrchní Lhota", "description" => "Vesnička na severovýchodě země", "founded" => 1447601504, "owner" => 1, "price" => 5000, "on_market" => false,
        ],
        [
          "id" => 10, "name" => "Velké Běliny", "description" => "Větší vesnice na jihu země", "founded" => 1455361687, "owner" => 0, "price" => 5000, "on_market" => false,
        ],
        [
          "id" => 11, "name" => "Roden", "description" => "Vesnice na západě země", "founded" => 1463914056, "owner" => 0, "price" => 5000, "on_market" => true,
        ],
      ])
      ->update();
    $this->execute("ALTER TABLE users AUTO_INCREMENT=2;");
    $this->table("monasteries")
      ->insert([
        [
          "id" => 1, "name" => "Pokusný klášter", "leader" => 0, "town" => 3, "founded" => 1446628777, "money" => 1001, "altair_level" => 1, "library_level" => 0, "hp" => 100,
        ],
      ])
      ->update();
    $this->table("users")
      ->insert([
        [
          "id" => 0, "publicname" => "Vladěna", "password" => '$2y$10$SKkWGjWJBlDDZcviLt0SXu5fNEaxsRAXlP82.nLZfq9gWN7n.qKe2', "email" => "admin@localhost", "joined" => 1, "last_active" => 1429779664, "last_prayer" => null, "last_transfer" => null, "group" => 0, "infomails" => false, "style" => "dark-sky", "gender" => "female", "life" => 60, "money" => 560132, "town" => 1, "monastery" => null, "prayers" => 0, "guild" => null, "guild_rank" => null, "order" => null, "order_rank" => null,
        ],
        [
          "id" => 1, "publicname" => "Trimadyl z Myhru", "password" => '$2y$10$SKkWGjWJBlDDZcviLt0SXu5fNEaxsRAXlP82.nLZfq9gWN7n.qKe2', "email" => "jakub.konecny2@centrum.cz", "joined" => 1429779664, "last_active" => 1475089811, "last_prayer" => null, "last_transfer" => 1455366455, "group" => 1, "infomails" => true, "style" => "dark-sky", "gender" => "male", "life" => 110, "money" => 18229, "town" => 2, "monastery" => null, "prayers" => 0, "guild" => null, "guild_rank" => null, "order" => null, "order_rank" => null,
        ],
      ])
      ->update();
    $this->table("castles")
      ->insert([
        [
          "id" => 1, "name" => "Dračí hrad", "description" => "Dračí hrad je již od nepaměti sídlem panovníků Nexendrie.", "founded" => 1429779664, "owner" => 0, "level" => 5, "hp" => 100,
        ],
      ])
      ->update();
  }

  public function down(): void {
    $this->execute("DELETE FROM castles");
    $this->execute("DELETE FROM users");
    $this->execute("DELETE FROM monasteries");
    $this->execute("DELETE FROM towns");
    $this->execute("DELETE FROM permissions");
    $this->execute("DELETE FROM `groups`");
    $this->execute("DELETE FROM order_ranks");
    $this->execute("DELETE FROM guild_ranks");
    $this->execute("DELETE FROM mount_types");
    $this->execute("DELETE FROM meals");
    $this->execute("DELETE FROM job_messages");
    $this->execute("DELETE FROM jobs");
    $this->execute("DELETE FROM skills");
    $this->execute("DELETE FROM item_sets");
    $this->execute("DELETE FROM items");
    $this->execute("DELETE FROM shops");
    $this->execute("DELETE FROM adventure_npcs");
    $this->execute("DELETE FROM adventures");
  }
}

?>