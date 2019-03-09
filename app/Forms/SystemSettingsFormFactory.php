<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\SettingsRepository;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Nexendrie\Orm\Model as ORM;

/**
 * Factory for form SystemSettings
 *
 * @author Jakub Konečný
 */
final class SystemSettingsFormFactory {
  /** @var SettingsRepository */
  protected $sr;
  /** @var ORM */
  protected $orm;
  /** @var string */
  protected $appDir;
  
  public function __construct(string $appDir, SettingsRepository $settingsRepository, ORM $orm) {
    $this->sr = $settingsRepository;
    $this->orm = $orm;
    $this->appDir = $appDir;
  }
  
  protected function getListOfGroups(): array {
    return $this->orm->groups->findAll()->fetchPairs("id", "singleName");
  }
  
  protected function getListOfTowns(): array {
    return $this->orm->towns->findAll()->fetchPairs("id", "name");
  }
  
  protected function getDefaultValues(): array {
    $settings = $this->sr->settings;
    return $settings;
  }
  
  /**
   * @todo use SettingsRepository to validate settings
   */
  public function create(): Form {
    $groups = $this->getListOfGroups();
    $form = new Form();
    $form->addGroup("Lokální nastavení");
    $locale = $form->addContainer("locale");
    $locale->addText("dateFormat", "Formát data:")
      ->setOption("description", "Pro funkci date()")
      ->setRequired("Zadej formát data.");
    $locale->addText("dateTimeFormat", "Formát času:")
      ->setOption("description", "Dokumentace na http://docs.php.net/manual/en/function.date.php")
      ->setRequired("Zadej formát času.");
    $form->addGroup("Role");
    $roles = $form->addContainer("roles");
    $roles->addSelect("guestRole", "Nepřihlášený uživatel:", $groups)
      ->setRequired("Vyber roli pro nepřihlášeného uživatele.");
    $roles->addSelect("loggedInRole", "Přihlášený uživatel:", $groups)
      ->setRequired("Vyber roli pro přihlášeného uživatele.");
    $roles->addSelect("bannedRole", "Zablokovaný uživatel:", $groups)
      ->setRequired("Vyber roli pro zablokovaného uživatele.");
    $form->addGroup("Stránkování");
    $pagination = $form->addContainer("pagination");
    $pagination->addText("articles", "Článků na stránku:")
      ->setRequired("Zadej počet novinek na stránku.")
      ->addRule(Form::INTEGER, "Počet novinek na stránku musí být číslo.");
    $form->addGroup("Nový uživatel");
    $newUser = $form->addContainer("newUser");
    $newUser->addSelect("style", "Výchozí vzhled:", UserSettingsFormFactory::getStylesList())
      ->setRequired("Zadej vzhled.");
    $newUser->addText("money", "Peníze:")
      ->setRequired("Zadej peníze.")
      ->addRule(Form::INTEGER, "Peníze musí být celé číslo.")
      ->addRule(Form::RANGE, "Peníze musí být v rozmezí 1-100.", [1, 100]);
    $newUser->addSelect("town", "Město:", $this->getListOfTowns())
      ->setRequired("Vyber město.");
    $form->addGroup("Daně a poplatky");
    $fees = $form->addContainer("fees");
    $fees->addText("incomeTax", "Daň z příjmů:")
      ->setOption("description", "% měsíčně")
      ->setRequired("Zadej daň z příjmů.")
      ->addRule(Form::INTEGER, "Daň z příjmů musí být celé číslo.")
      ->addRule(Form::RANGE, "Daň z příjmů musí být v rozmezí 0-100.", [0, 100]);
    $fees->addText("loanInterest", "Úrok z půjčky:")
      ->setOption("description", "% ročně")
      ->setRequired("Zadej úrok z půjčky.")
      ->addRule(Form::INTEGER, "Úrok z půjčky musí být celé číslo.")
      ->addRule(Form::RANGE, "Úrok z půjčky musí být v rozmezí 0-100.", [0, 100]);
    $fees->addText("depositInterest", "Úrok u termínovaných vkladů:")
      ->setOption("description", "% ročně")
      ->setRequired("Zadej úrok u termínovaných vkladů.")
      ->addRule(Form::INTEGER, "Úrok u termínovaných vkladů musí být celé číslo.")
      ->addRule(Form::RANGE, "Úrok u termínovaných vkladů musí být v rozmezí 0-100.", [0, 100]);
    $fees->addText("buildMonastery", "Založení kláštera:")
      ->setOption("description", "Cena založení kláštera v groších.")
      ->setRequired("Zadej cenu založení kláštera.")
      ->addRule(Form::INTEGER, "Cena založení kláštera musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena založení kláštera musí být v rozmezí 0-5000.", [0, 5000]);
    $fees->addText("buildCastle", "Stavba hradu:")
      ->setOption("description", "Cena stavby hradu v groších.")
      ->setRequired("Zadej cenu stavby hradu.")
      ->addRule(Form::INTEGER, "Cena stavby hradu musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena stavby hradu musí být v rozmezí 0-5000.", [0, 5000]);
    $fees->addText("foundGuild", "Založení cechu:")
      ->setOption("description", "Cena založení cechu v groších.")
      ->setRequired("Zadej cenu založení cechu.")
      ->addRule(Form::INTEGER, "Cena založení cechu musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena založení cechu musí být v rozmezí 0-5000.", [0, 5000]);
    $fees->addText("foundOrder", "Založení řádu:")
      ->setOption("description", "Cena založení řádu v groších.")
      ->setRequired("Zadej cenu založení řádu.")
      ->addRule(Form::INTEGER, "Cena založení řádu musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena založení řádu musí být v rozmezí 0-5000.", [0, 5000]);
    $fees->addText("foundTown", "Založení města:")
      ->setOption("description", "Cena založení města v groších.")
      ->setRequired("Zadej cenu založení města.")
      ->addRule(Form::INTEGER, "Cena založení města musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena založení města musí být v rozmezí 0-5000.", [0, 5000]);
    $fees->addText("autoFeedMount", "Automatické krmení jezdeckého zvířete:")
      ->setOption("description", "Cena automatické krmení jezdeckého zvířete v groších (za týden).")
      ->setRequired("Zadej cenu automatické krmení jezdeckého zvířete.")
      ->addRule(Form::INTEGER, "Cena automatické krmení jezdeckého zvířete musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena automatické krmení jezdeckého zvířete musí být v rozmezí 0-20.", [0, 20]);
    $form->addGroup("Budovy");
    $buildings = $form->addContainer("buildings");
    $buildings->addText("weeklyWearingOut", "Týdenní opotřebení:")
      ->setOption("description", "%")
      ->setRequired("Zadej týdenní opotřebení budov.")
      ->addRule(Form::INTEGER, "Týdenní opotřebení budov musí být celé číslo.")
      ->addRule(Form::RANGE, "Týdenní opotřebení budov musí být v rozmezí 0-100.", [0, 100]);
    $buildings->addText("criticalCondition", "Kritický stav:")
      ->setOption("description", "%, při poklesu pod tuto hodnotu budova přestane poskytovat výhody")
      ->setRequired("Zadej kritický stav budov.")
      ->addRule(Form::INTEGER, "Kritický stav budov musí být celé číslo.")
      ->addRule(Form::RANGE, "Kritický stav budov musí být v rozmezí 0-100.", [0, 100]);
    $form->addGroup("Registrace");
    $registration = $form->addContainer("registration");
    $registration->addText("token", "Heslo:")
      ->setOption("description", "Ponech prázdné, pokud má být registrace přístupná všem.");
    $form->addGroup("Stránky");
    $site = $form->addContainer("site");
    $site->addText("versionSuffix", "Přípona verze:")
      ->addRule(Form::MAX_LENGTH, null, 5)
      ->setRequired(false);
    $form->setCurrentGroup(null);
    $form->addSubmit("submit", "Uložit změny");
    $form->setDefaults($this->getDefaultValues());
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    $filename = $this->appDir . "/config/local.neon";
    $config = Neon::decode(file_get_contents($filename));
    $config += ["nexendrie" => $values];
    try {
      $content = Neon::encode($config, Neon::BLOCK);
      FileSystem::write($filename, $content);
    } catch(\Nette\IOException $e) {
      $form->addError("Došlo k chybě při ukládání nastavení. Ujisti se, že máš právo zápisu do souboru.");
    }
  }
}
?>