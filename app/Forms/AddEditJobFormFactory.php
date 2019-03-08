<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditJob
 *
 * @author Jakub Konečný
 */
final class AddEditJobFormFactory {
  /** @var \Nexendrie\Model\Skills */
  protected $skillsModel;
  
  public function __construct(\Nexendrie\Model\Skills $skillsModel) {
    $this->skillsModel = $skillsModel;
  }
  
  /**
   * @return string[]
   */
  protected function getSkills(): array {
    return $this->skillsModel->listOfSkills("work")->fetchPairs("id", "name");
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.")
      ->setOption("description", "Zobrazí se v seznamu prací.");
    $form->addTextArea("help", "Nápověda:")
      ->setRequired("Zadej nápověda.")
      ->setOption("description", "Zobrazí se během práce. %count% se nahradí počtem požadovaných směn (nebo 1), %reward% odměnou za 1 směnu nebo celkovou odměnou.");
    $form->addText("count", "Počet:")
      ->setRequired("Zadej počet.")
      ->addRule(Form::INTEGER, "Počet musí být celé číslo.")
      ->setValue(0);
    $form->addText("award", "Odměna:")
      ->setRequired("Zadej odměnu.")
      ->addRule(Form::INTEGER, "Odměna musí být celé číslo.")
      ->setOption("description", "Odměna za dokončení práce/1 jednotku.");
    $form->addText("shift", "Délka směny")
      ->setRequired("Zadej délku směny.")
      ->addRule(Form::INTEGER, "Délka směny musí být celé číslo.")
      ->setOption("description", "Délka 1 směny v minutách.");
    $form->addText("level", "Úroveň:")
      ->setRequired("Zadej úroveň.")
      ->addRule(Form::INTEGER, "Úroveň musí být celé číslo.")
      ->addRule(Form::RANGE, "Úroveň musí být v rozmezí 50-10000.", [50, 10000])
      ->setValue(50)
      ->setOption("description", "Minimální úroveň pro výkon práce.");
    $form->addSelect("neededSkill", "Dovednost:", $this->getSkills())
      ->setPrompt("Vyber dovednost")
      ->setRequired("Vyber dovednost.")
      ->setOption("description", "Dovednost nutná pro výkon práce, zvyšuje příjem.");
    $form->addText("neededSkillLevel", "Úroveň dovednosti:")
      ->setRequired("Zadej úroveň dovednosti.")
      ->addRule(Form::INTEGER, "Úroveň dovednosti musí být celé číslo.")
      ->addRule(Form::RANGE, "Úroveň dovednosti musí být v rozmezí 0-5.", [0, 5])
      ->setValue(0);
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>