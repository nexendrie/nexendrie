<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\Group,
    Nexendrie\Model\SettingsRepository,
    Nette\Utils\ArrayHash;

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
  
  function __construct(SettingsRepository $settingsRepository, Group $groupModel) {
    $this->sr = $settingsRepository;
    $this->groupModel = $groupModel;
  }
  
  /**
   * @return array
   */
  protected function getListOfGroups() {
    $return = array();
    $groups = $this->groupModel->listOfGroups();
    foreach($groups as $group) {
      $return[$group->id] = $group->singleName;
    }
    return $return;
  }
  
  /**
   * @return array
   */
  protected function getDefaultValues() {
    $plural = $this->sr->settings["locale"]["plural"];
    for($i = 3; $i <= 5; $i++) {
      unset($plural[$i]);
    }
    return array(
      "dateFormat" => $this->sr->settings["locale"]["dateFormat"],
      "dateTimeFormat" => $this->sr->settings["locale"]["dateTimeFormat"],
      "plural" => implode("\n", $plural),
      "guestRole" => $this->sr->settings["roles"]["guestRole"],
      "loggedInRole" => $this->sr->settings["roles"]["loggedInRole"],
      "bannedRole" => $this->sr->settings["roles"]["bannedRole"]
    );
  }
  
  /**
   * @return Form
   */
  function create() {
    $groups = $this->getListOfGroups();
    $form = new Form;
    $form->addGroup("Lokální nastavení");
    $form->addText("dateFormat", "Formát datumu:")
      ->setOption("description", "Pro funkci date()")
      ->setRequired("Zadej formát datumu");
    $form->addText("dateTimeFormat", "Formát času:")
      ->setOption("description", "Dokumentace na http://docs.php.net/manual/en/function.date.php")
      ->setRequired("Zadej formát času");
    $form->addTextArea("plural", "Plurály:")
      ->setRequired("Zadej plurály");
    $form->addGroup("Role");
    $form->addSelect("guestRole", "Nepřihlášený uživatel:", $groups)
      ->setRequired("Vyyber roli pro nepřihlášeného uživatele.");
    $form->addSelect("loggedInRole", "Přihlášený uživatel:", $groups)
      ->setRequired("Vyyber roli pro přihlášeného uživatele.");
    $form->addSelect("bannedRole", "Zablokovaný uživatel:", $groups)
      ->setRequired("Vyyber roli pro zablokovaného uživatele.");
    $form->currentGroup = NULL;
    $form->addSubmit("submit", "Uložit změny");
    $form->setDefaults($this->getDefaultValues());
    $form->onValidate[] = array($this, "validate");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @return void
   */
  function validate(Form $form) {
    $values = $form->getValues();
    $plural = explode("\n", $values["plural"]);
    if(count($plural) != 3) $form->addError("Plurály musí obsahovat právě 3 řádky.");
    if(is_int($plural[0])) $form->addError("První plurál musít být číslo.");
    if(is_int($plural[2])) $form->addError("Třetí plurál musít být číslo.");
  }
  
  function submitted(Form $form, ArrayHash $values) {
    $settings = array(
      "roles" => array(
        "guestRole" => $values["guestRole"],
        "loggedInRole" => $values["loggedInRole"],
        "bannedRole" => $values["bannedRole"]
      ),
      "locale" => array(
        "dateFormat" => $values["dateFormat"],
        "dateTimeFormat" => $values["dateTimeFormat"],
        "plural" => explode("\n", $values["plural"])
      )
    );
    try {
      $this->sr->save($settings);
    } catch (\Nette\IOException $e) {
      $form->addError("Došlo k chybě při ukládání nastavení. Ujisti se, že máš právo zápisu do souboru.");
    }
  }
}
?>