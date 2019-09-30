<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\Skill as SkillEntity;

/**
 * Factory for form AddEditSkill
 *
 * @author Jakub Konečný
 */
final class AddEditSkillFormFactory {
  /** @var \Nexendrie\Model\Skills */
  protected $model;
  /** @var SkillEntity|null */
  protected $skill;

  public function __construct(\Nexendrie\Model\Skills $model) {
    $this->model = $model;
  }

  public function create(?SkillEntity $skill = null): Form {
    $this->skill = $skill;
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
    $type = $form->addSelect("type", "Typ:", SkillEntity::getTypes())
      ->setRequired("Vyber typ.");
    $form->addSelect("stat", "Vlastnost:", SkillEntity::getStats())
      ->setPrompt("žádná")
      ->addConditionOn($type, Form::EQUAL, SkillEntity::TYPE_COMBAT)
        ->setRequired("Vyber vlasnost.");
    $form->addText("statIncrease", "Vylepšení vlastnosti:")
      ->setValue(0)
      ->addConditionOn($type, Form::EQUAL, SkillEntity::TYPE_COMBAT)
        ->setRequired()
        ->addRule(Form::INTEGER, "Vylepšení vlastnosti musí být celé číslo.")
        ->addRule(Form::RANGE, "Vylepšení vlastnosti musí být v rozmezí 1-99.", [1, 99]);
    $form->addSubmit("submit", "Odeslat");
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "process"];
    if($skill !== null) {
      $form->setDefaults($skill->toArray());
    }
    return $form;
  }

  public function validate(Form $form, array $values): void {
    if($values["type"] === SkillEntity::TYPE_WORK && $values["stat"] !== null) {
      $form->addError("Neplatná kombinace: vybrána vlastnost u pracovní dovednosti.");
    }
    if($values["type"] === SkillEntity::TYPE_WORK && $values["statIncrease"] !== 0) {
      $form->addError("Neplatná kombinace: vylepšení dovednosti musí být 0 u pracovní dovednosti.");
    }
  }

  public function process(Form $form, array $values): void {
    if($this->skill === null) {
      $this->model->add($values);
    } else {
      $this->model->edit($this->skill->id, $values);
    }
  }
}
?>