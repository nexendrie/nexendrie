<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Job;
use Nexendrie\Model\Skills;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditJob
 *
 * @author Jakub Konečný
 */
final class AddEditJobFormFactory {
  private ?\Nexendrie\Orm\Job $job;

  public function __construct(private readonly Job $model, private readonly Skills $skillsModel) {
  }

  /**
   * @return string[]
   */
  private function getSkills(): array {
    return $this->skillsModel->listOfSkills("work")->fetchPairs("id", "name");
  }
  
  public function create(?\Nexendrie\Orm\Job $job = null): Form {
    $this->job = $job;
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
    $form->onSuccess[] = [$this, "process"];
    if($job !== null) {
      $form->setDefaults($job->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if($this->job === null) {
      $this->model->addJob($values);
    } else {
      $this->model->editJob($this->job->id, $values);
    }
  }
}
?>