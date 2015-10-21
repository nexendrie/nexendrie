<?php
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
  
  function __construct(\Nexendrie\Model\Skills $model, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/academy.latte");
    $skillsRows = $this->model->listOfSkills();
    $skills = array();
    foreach($skillsRows as $skill) {
      $s = (object) array(
        "id" => $skill->id, "name" => $skill->name
      );
      $s->level = $this->model->getLevelOfSkill($skill->id);
      $price = $this->model->calculateLearningPrice($skill->price, $s->level + 1);
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
  function handleLearn($skillId) {
    try {
      $this->model->learn($skillId);
      $this->presenter->flashMessage("Úspěšně jsi se naučil dovednost.");
    } catch(SkillNotFoundException $e) {
      $this->presenter->flashMessage("Dovednost nenalezena.");
    } catch(SkillMaxLevelReachedException $e) {
      $this->presenter->flashMessage("Dosáhl jsi již maximální úrovně.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    }
  }
}

interface AcademyControlFactory {
  /** @return AcademyControl */
  function create();
}
?>