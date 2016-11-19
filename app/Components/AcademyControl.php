<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\SkillNotFoundException,
    Nexendrie\Model\SkillMaxLevelReachedException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Orm\User as UserEntity;

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
  
  function __construct(\Nexendrie\Model\Skills $model, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  /**
   * @param string $type
   * @return void
   */
  function render(string $type = "work") {
    $template = $this->template;
    $template->setFile(__DIR__ . "/academy.latte");
    $types = ["work", "combat"];
    if(!in_array($type, $types)) {
      $type = "work";
    }
    $template->type = $type;
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
    $template->skills = $skills;
    $template->render();
  }
  
  /**
   * @param int $skillId
   * @return void
   */
  function handleLearn(int $skillId) {
    try {
      $this->model->learn($skillId);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Úspěšně jsi se naučila dovednost.";
      } else {
        $message = "Úspěšně jsi se naučil dovednost.";
      }
      $this->presenter->flashMessage($message);
    } catch(SkillNotFoundException $e) {
      $this->presenter->flashMessage("Dovednost nenalezena.");
    } catch(SkillMaxLevelReachedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Dosáhla jsi již maximální úrovně.";
      } else {
        $message = "Dosáhl jsi již maximální úrovně.";
      }
      $this->presenter->flashMessage($message);
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    }
    $this->presenter->redirect("default");
  }
}

interface AcademyControlFactory {
  /** @return AcademyControl */
  function create();
}
?>