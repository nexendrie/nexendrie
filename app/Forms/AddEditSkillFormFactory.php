<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\Skill as SkillEntity;

/**
 * Factory for form AddEditSkill
 *
 * @author Jakub Konečný
 */
final class AddEditSkillFormFactory {
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
    $form->addText("price", "Cena:")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 1-999.", [1, 999])
      ->setOption("description", "Cena na první úrovni");
    $form->addText("maxLevel", "Úrovní:")
      ->setRequired("Zadej počet úrovní.")
      ->addRule(Form::INTEGER, "Počet úrovní musí být celé číslo.")
      ->addRule(Form::RANGE, "Počet úrovní musí být v rozmezí 1-99.", [1, 99])
      ->setValue(5);
    $form->addSelect("type", "Typ:", SkillEntity::getTypes())
      ->setRequired("Vyber typ.");
    $form->addSelect("stat", "Vlastnost:", SkillEntity::getStats())
      ->setPrompt("žádná")
      ->addConditionOn($form["type"], Form::EQUAL, "combat")
        ->setRequired("Vyber vlasnost.");
    $form->addText("statIncrease", "Vylepšení vlastnosti:")
      ->setValue(0)
      ->addConditionOn($form["type"], Form::EQUAL, "combat")
        ->setRequired()
        ->addRule(Form::INTEGER, "Vylepšení vlastnosti musí být celé číslo.")
        ->addRule(Form::RANGE, "Vylepšení vlastnosti musí být v rozmezí 1-99.", [1,99]);
    $form->addSubmit("submit", "Odeslat");
    $form->onValidate[] = [$this, "validate"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   */
  public function validate(Form $form, array $values): void {
    if($values["type"] === "work" AND $values["stat"] != NULL) {
      $form->addError("Neplatná kombinace: vybrána vlastnost u pracovní dovednosti.");
    }
    if($values["type"] === "work" AND $values["statIncrease"] != 0) {
      $form->addError("Neplatná kombinace: vylepšení dovednosti musí být 0 u pracovní dovednosti.");
    }
  }
}
?>