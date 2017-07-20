<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\SkillNotFoundException,
    Nexendrie\Model\SkillMaxLevelReachedException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * AcademyControl
 *
 * @author Jakub Konečný
 */
class AcademyControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Skills */
  protected $model;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nette\Security\User */
  protected $user;
  
  public function __construct(\Nexendrie\Model\Skills $model, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  public function render(string $type = "work"): void {
    $this->template->setFile(__DIR__ . "/academy.latte");
    $types = ["work", "combat"];
    if(!in_array($type, $types)) {
      $type = "work";
    }
    $this->template->type = $type;
    $skillsRows = $this->model->listOfSkills($type);
    $skills = [];
    foreach($skillsRows as $skill) {
      $s = (object) [
        "id" => $skill->id, "name" => $skill->name, "maxLevel" => $skill->maxLevel,
        "effect" => $skill->effect
      ];
      $s->level = $this->model->getLevelOfSkill($skill->id);
      $price = $this->model->calculateLearningPrice($skill->price, $s->level + 1, $s->maxLevel);
      $s->price = $this->localeModel->money($price);
      $skills[] = $s;
    }
    $this->template->skills = $skills;
    $this->template->render();
  }
  
  public function handleLearn(int $skillId): void {
    try {
      $this->model->learn($skillId);
      $message = $this->localeModel->genderMessage("Úspěšně jsi se naučil(a) dovednost.");
      $this->presenter->flashMessage($message);
    } catch(SkillNotFoundException $e) {
      $this->presenter->flashMessage("Dovednost nenalezena.");
    } catch(SkillMaxLevelReachedException $e) {
      $message = $this->localeModel->genderMessage("Dosáhl(a) jsi již maximální úrovně.");
      $this->presenter->flashMessage($message);
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    }
    $this->presenter->redirect("default");
  }
}
?>