<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

// phpcs:disable Generic.Files.LineLength
final class ArticleSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class];
    }

    public function run(): void
    {
        $this->table("articles")
            ->insert([
                [
                    'id' => 1,
                    'title' => 'Nový web',
                    'text' => 'Příprava nového webu je v plném proudu.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1434745292,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 2,
                    'title' => 'Pokusná novinka',
                    'text' => 'Test text test',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1434750242,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 3,
                    'title' => 'Přechod na Nextras\Orm',
                    'text' => 'Probíhá přechod z Nette\Database na Nextras\Orm.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1441225044,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 4,
                    'title' => 'Žádné zprávy ...',
                    'text' => 'Jak jste si možná všimli, v Nexendrii se toho poslední dobou moc nedělo (vlastně vůbec nic).',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1444061049,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 5,
                    'title' => 'Práce',
                    'text' => 'V Nexendrii právě vznikají povolání. Povolání se vybírá na týden, po ukončení dostaneš odpovídají odměnu.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1444608626,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 6,
                    'title' => 'Smršť změn',
                    'text' => "V posledn\xc3\xadm t\xc3\xbddnu prob\xc4\x9bhlo v Nexendrii mnoho zm\xc4\x9bn. Pokus\xc3\xadm se shrnout ty nejpodstatn\xc4\x9bj\xc5\xa1\xc3\xad.\nNejd\xc5\x99\xc3\xadve byly p\xc5\x99id\xc3\xa1ny jezdeck\xc3\xa1 zv\xc3\xad\xc5\x99ata. Daj\xc3\xad se koupit na tr\xc5\xbenici a je nutn\xc3\xa9 o n\xc4\x9b pravideln\xc4\x9b pe\xc4\x8dovat a krmit je ve st\xc3\xa1j\xc3\xadch. Prozat\xc3\xadm nemaj\xc3\xad \xc5\xbe\xc3\xa1dn\xc3\xa9 vyu\xc5\xbeit\xc3\xad, ale do budoucna se po\xc4\x8d\xc3\xadt\xc3\xa1, \xc5\xbee budou vyu\xc5\xbeiti p\xc5\x99i cest\xc3\xa1ch do vzd\xc3\xa1len\xc3\xbdch kout\xc5\xaf nejen Nexendrie ale i okoln\xc3\xadch zem\xc3\xad.\nN\xc3\xa1sledn\xc4\x9b byly zalo\xc5\xbeeny nov\xc3\xa1 m\xc4\x9bsta a vesnice, aby pojmuly vzr\xc5\xafstaj\xc3\xadc\xc3\xad populaci zem\xc4\x9b. Ka\xc5\xbed\xc3\xa9 m\xc4\x9bsto a vesnice je vlastn\xc4\x9bno \xc5\xa1lechticem nebo vy\xc5\xa1\xc5\xa1\xc3\xadm duchovn\xc3\xadm, kte\xc5\x99\xc3\xad s nimi mohou obchodovat. \nD\xc3\xa1le vznikly v ka\xc5\xbed\xc3\xa9m m\xc4\x9bst\xc4\x9b a vesnici akademie. V nich se m\xc5\xaf\xc5\xbeete za m\xc3\xadrn\xc3\xbd poplatek nau\xc4\x8dit dovednosti, kter\xc3\xa9 v\xc3\xa1m umo\xc5\xben\xc3\xad vykon\xc3\xa1vat nov\xc3\xa9 pr\xc3\xa1ce a z\xc3\xadskat z nich v\xc4\x9bt\xc5\xa1\xc3\xad odm\xc4\x9bnu. Ka\xc5\xbed\xc3\xa1 dovednost m\xc3\xa1 5 \xc3\xbarovni.\nPosledn\xc3\xad v\xc3\xbdznamnou zm\xc4\x9bnou bylo postaven\xc3\xad v\xc4\x9bzen\xc3\xad, kde budou pos\xc3\xadl\xc3\xa1ni pravidla poru\xc5\xa1uj\xc3\xadc\xc3\xad obyvatel\xc3\xa9 Nexendrie. Budou tam nuceni pracovat dobu \xc3\xbam\xc4\x9brnou jejich p\xc5\x99e\xc4\x8dinu.",
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1445190658,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 7,
                    'title' => 'Test',
                    'text' => 'Lorem ipsum dota',
                    'author' => 1,
                    'category' => 'chronicle',
                    'created' => 1445263644,
                    'allowed_comments' => false,
                ],
                [
                    'id' => 8,
                    'title' => 'Shrnutí novinek',
                    'text' => 'Za poslední dva týdny se toho v Nexendrii událo příliš mnoho, proto se o všem zmíním jen letmo, podrobnosti můžete najít v Nápovědě. Zaprvé, rodina Žajských expandovala se svou bankou do každé vesnice a každého města v království. Dále byly vybudovány hostince a žaláře. Také přibyla (měšťanům, mnichům a šlechticům) možnost podnikat dobrodružství. Prvním je Zloděj ovcí, další brzy přibudou. Většině obyvatel země přibyla nepříjemná povinnost měsíčně odvádět daně své vrchnosti. Daní jsou nyní zatíženy příjmy z prací a dobrodružství. Naopak šlechta a vyšší duchovní mají nyní možnost ve svých městech a vesnicích tuto daň vybírat.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1446592023,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 9,
                    'title' => 'Vzestup církví',
                    'text' => 'Jak jistě všichni víte, v Nexendrii existuje vícero církví. V různých částech království mají různou moc a mnohdy vládnou i celým městům. Ale včera se stalo něco neočekávaného: otevřely dveře svých doposud uzavřených klášterů všem, kteří se chtějí připojit. Život v klášteře přináší často přísná pravidla a mnoho odříkání.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1446769232,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 10,
                    'title' => 'Cechy',
                    'text' => 'V městech a vesnicích Nexendrie začínají vznikat cechy. Ty přijímají do svých řad sedláky a měšťany z 1 města či vesnice a zvyšují jejich příjmy z práce. Avšak každý člen musí každý měsíc platit členský poplatek, jehož výše závisí na postavení v cechů. Podrobnosti můžete najít v nápovědě.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1453732535,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 11,
                    'title' => 'Řády',
                    'text' => 'V Nexendrii začínají vznikat řády. Řád je prestižní organizace, ve které se sdružují šlechtici a která zvyšuje jeho členům příjem z dobrodružství. Každý člen musí měsíčně odvádět členský příspěvek podle své hodnosti, z vybraných měsíc může být řád vylepšen, což dále zvyšuje příjmy členů z dobrodružství. Založit řád mohou jen příslušníci vyšší šlechty (páni, vévodové a členové Korunní rady) a stojí je to 1200 grošů.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1465133303,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 12,
                    'title' => 'Manželství',
                    'text' => 'V Nexendrii je nyní možné uzavřít manželství. Prozatím z něj neplynou žádné výhody, ale brzy jistě nějaké přibudou. Další podrobnosti, včetně vysvětlení jak uzavřít manželství, naleznete v nápovědě.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1466943412,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 13,
                    'title' => 'Změny v přihlašování',
                    'text' => "Ve v\xc3\xbdvojov\xc3\xa9 verzi Nexendrie zmizelo u\xc5\xbeivatelsk\xc3\xa9 jm\xc3\xa9no a pou\xc5\xbe\xc3\xadv\xc3\xa1 se m\xc3\xadsto n\xc4\x9bj p\xc5\x99i p\xc5\x99ihla\xc5\xa1ov\xc3\xa1n\xc3\xad e-mailov\xc3\xa1 adresa. Jm\xc3\xa9no, kter\xc3\xa9 se zobrazuje ostatn\xc3\xadm, z\xc5\xafst\xc3\xa1v\xc3\xa1, ale nyn\xc3\xad se zad\xc3\xa1v\xc3\xa1 p\xc5\x99\xc3\xadmo p\xc5\x99i registraci (d\xc5\x99\xc3\xadve bylo stejn\xc3\xa9 jako u\xc5\xbeivatelsk\xc3\xa9 jm\xc3\xa9no, s t\xc3\xadm \xc5\xbee \xc5\xa1lo pozd\xc4\x9bji zm\xc4\x9bnit).\nD\xc5\xafvodem je, \xc5\xbee 2 jm\xc3\xa9na byla matouc\xc3\xad (a jen 1 z nich \xc5\xa1lo m\xc4\x9bnit) a nav\xc3\xadc e-mailov\xc3\xa1 adresa nebyla jinak vyu\xc5\xbeit\xc3\xa1.",
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1551718915,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 14,
                    'title' => 'Přejmenován vévodský titul',
                    'text' => "Z na\xc5\x99\xc3\xadzen\xc3\xad Jej\xc3\xadho Veli\xc4\x8denstva se s okam\xc5\xbeitou platnost\xc3\xad m\xc4\x9bn\xc3\xad titul v\xc3\xa9voda (nejvy\xc5\xa1\xc5\xa1\xc3\xad b\xc4\x9b\xc5\xben\xc3\xbd \xc5\xa1lechtick\xc3\xbd titul) na markrab\xc4\x9b.\nOd\xc5\xafvodn\xc4\x9bn\xc3\xad: v\xc3\xa9vodsk\xc3\xbd titul je norm\xc3\xa1ln\xc4\x9b vy\xc5\xa1\xc5\xa1\xc3\xad ne\xc5\xbe kn\xc3\xad\xc5\xbeec\xc3\xad (tento titul maj\xc3\xad \xc4\x8dlenov\xc3\xa9 Korunn\xc3\xad rady a tak\xc3\xa9 2 kn\xc3\xad\xc5\xbeec\xc3\xad tituly m\xc3\xa1 i panovn\xc3\xadk Nexendrie), v\xc3\xa9voda m\xc5\xaf\xc5\xbee b\xc3\xbdt i suver\xc3\xa9nn\xc3\xad panovn\xc3\xadk (stejn\xc4\x9b jako kn\xc3\xad\xc5\xbee a na rozd\xc3\xadl od markrab\xc4\x9bte) a tak\xc3\xa9 v\xc3\xa9vodsk\xc3\xbd titul je od \xc4\x8das\xc5\xaf c\xc3\xadsa\xc5\x99stv\xc3\xad v\xc5\xa1eobecn\xc4\x9b nen\xc3\xa1vid\xc4\x9bn.\nDodatek: v\xc5\xa1em dot\xc4\x8den\xc3\xbdm osob\xc3\xa1m se na jejich profilech u\xc5\xbe zobrazuje nov\xc3\xbd titul, n\xc3\xa1pov\xc4\x9bda v beta verzi bude aktualizov\xc3\xa1na p\xc5\x99i nasazen\xc3\xad p\xc5\x99\xc3\xad\xc5\xa1t\xc3\xad verze (podle pl\xc3\xa1nu 1. \xc4\x8dervna), v alfa verzi se tak ji\xc5\xbe stalo.",
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1551719606,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 15,
                    'title' => 'Dostupnější jezdecká zvířata',
                    'text' => 'U některých typů jezdeckých zvířat byl právě snížen požadovaný titul pro jejich vlastnictví. Konkrétně osli jsou nyní dostupní pro vsechny měšťany, nejen konšely (a kněží a šlechtice). A draka si nyní může pořídit každý vyšší šlechtic, nejen markrabata a členové Korunní rady.',
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1552308070,
                    'allowed_comments' => true,
                ],
                [
                    'id' => 16,
                    'title' => 'Oslabení církví',
                    'text' => "Po n\xc4\x9bkolika letech sna\xc5\xbeen\xc3\xad se kr\xc3\xa1lovn\xc4\x9b poda\xc5\x99ilo prosadit z\xc3\xa1kon, kter\xc3\xbd zakazuje velekn\xc4\x9b\xc5\xbe\xc3\xadm vlastnit m\xc4\x9bsta a vesnice. U kr\xc3\xa1lovsk\xc3\xa9ho dvora p\xc5\x99evl\xc3\xa1dal n\xc3\xa1zor, \xc5\xbee c\xc3\xadrkev by m\xc4\x9bla vlastnit pouze kl\xc3\xa1\xc5\xa1tery a nezasahovat do \xc5\x99\xc3\xadzen\xc3\xad m\xc4\x9bst a vesnic. Nyn\xc3\xad je mohou na trhu koupit pouze \xc5\xa1lechtici (vy\xc5\xa1\xc5\xa1\xc3\xad i ni\xc5\xbe\xc5\xa1\xc3\xad) a Korunn\xc3\xad rada se zav\xc3\xa1zala, \xc5\xbee je p\xc5\x99estane ud\xc4\x9blovat velekn\xc4\x9b\xc5\xbe\xc3\xadm.\nTato zm\xc4\x9bna je prozat\xc3\xadm pouze na alfa verzi, na bet\xc4\x9b se objev\xc3\xad 1. \xc4\x8dervna.",
                    'author' => 1,
                    'category' => 'news',
                    'created' => 1558267593,
                    'allowed_comments' => true,
                ],
            ])
            ->update();
    }
}
