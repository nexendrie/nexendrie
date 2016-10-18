<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\Group,
    Nexendrie\Model\SettingsRepository,
    Nexendrie\Model\Town;

/**
 * Factory for form SystemSettings
 *
 * @author Jakub Konečný
 */
class SystemSettingsFormFactory {
  /** @var SettingsRepository */
  protected $sr;
  /** @var Group */
  protected $groupModel;
  /** @var Town */
  protected $townModel;
  
  function __construct(SettingsRepository $settingsRepository, Group $groupModel, Town $townModel) {
    $this->sr = $settingsRepository;
    $this->groupModel = $groupModel;
    $this->townModel = $townModel;
  }
  
  /**
   * @return array
   */
  protected function getListOfGroups(): array {
    $return = [];
    $groups = $this->groupModel->listOfGroups();
    foreach($groups as $group) {
      $return[$group->id] = $group->singleName;
    }
    return $return;
  }
  
  /**
   * @return array
   */
  protected function getListOfTowns(): array {
    $return = [];
    $towns = $this->townModel->listOfTowns();
    foreach($towns as $town) {
      $return[$town->id] = $town->name;
    }
    return $return;
  }
  
  /**
   * @return array
   */
  protected function getDefaultValues(): array {
    $settings = $this->sr->settings;
    for($i = 3; $i <= 5; $i++) {
      unset($settings["locale"]["plural"][$i]);
    }
    $settings["locale"]["plural"] = implode("\n", $settings["locale"]["plural"]);
    return $settings;
  }
  
  /**
   * @return Form
   */
  function create(): Form {
    $groups = $this->getListOfGroups();
    $form = new Form;
    $form->addGroup("Lokální nastavení");
    $locale = $form->addContainer("locale");
    $locale->addText("dateFormat", "Formát datumu:")
      ->setOption("description", "Pro funkci date()")
      ->setRequired("Zadej formát datumu.");
    $locale->addText("dateTimeFormat", "Formát času:")
      ->setOption("description", "Dokumentace na http://docs.php.net/manual/en/function.date.php")
      ->setRequired("Zadej formát času.");
    $locale->addTextArea("plural", "Plurály:")
      ->setRequired("Zadej plurály.");
    $form->addGroup("Role");
    $roles = $form->addContainer("roles");
    $roles->addSelect("guestRole", "Nepřihlášený uživatel:", $groups)
      ->setRequired("Vyyber roli pro nepřihlášeného uživatele.");
    $roles->addSelect("loggedInRole", "Přihlášený uživatel:", $groups)
      ->setRequired("Vyyber roli pro přihlášeného uživatele.");
    $roles->addSelect("bannedRole", "Zablokovaný uživatel:", $groups)
      ->setRequired("Vyyber roli pro zablokovaného uživatele.");
    $form->addGroup("Stránkování");
    $pagination = $form->addContainer("pagination");
    $pagination->addText("news", "Novinek na stránku:")
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
    $form->addGroup("Registrace");
    $registration = $form->addContainer("registration");
    $registration->addText("token", "Heslo:")
      ->setOption("description", "Ponech prázdné, pokud má být registrace přístupná všem.");
    $form->addGroup("Stránky");
    $site = $form->addContainer("site");
    $site->addText("versionSuffix", "Přípona verze:")
      ->addRule(Form::MAX_LENGTH, NULL, 5)
      ->setRequired(false);
    $form->setCurrentGroup(NULL);
    $form->addSubmit("submit", "Uložit změny");
    $form->setDefaults($this->getDefaultValues());
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function validate(Form $form, array $values) {
    $plural = explode("\n", $values["locale"]["plural"]);
    if(count($plural) != 3) $form->addError("Plurály musí obsahovat právě 3 řádky.");
    if(is_int($plural[0])) $form->addError("První plurál musít být číslo.");
    if(is_int($plural[2])) $form->addError("Třetí plurál musít být číslo.");
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values) {
    try {
      $this->sr->save($values);
    } catch(\Nette\IOException $e) {
      $form->addError("Došlo k chybě při ukládání nastavení. Ujisti se, že máš právo zápisu do souboru.");
    }
  }
}
?>