<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\SkillNotFoundException;
use Nexendrie\Model\SkillMaxLevelReachedException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Utils\Constants;
use Nexendrie\Orm\Skill;

/**
 * AcademyControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class AcademyControl extends \Nette\Application\UI\Control {
  protected \Nexendrie\Model\Skills $model;
  protected \Nexendrie\Model\Locale $localeModel;
  protected \Nette\Security\User $user;
  
  public function __construct(\Nexendrie\Model\Skills $model, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  /**
   * @return string[]
   */
  protected function getSkillTypes(): array {
    return Constants::getConstantsValues(Skill::class, "TYPE_");
  }
  
  public function render(string $type = "work"): void {
    $this->template->setFile(__DIR__ . "/academy.latte");
    $types = $this->getSkillTypes();
    if(!in_array($type, $types, true)) {
      $type = "work";
    }
    $this->template->type = $type;
    $skillsRows = $this->model->listOfSkills($type);
    $skills = [];
    foreach($skillsRows as $skill) {
      $skills[] = $this->model->getUserSkill($skill->id);
    }
    $this->template->skills = $skills;
    $this->template->render();
  }
  
  public function handleLearn(int $skill): void {
    try {
      $this->model->learn($skill);
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
    $this->presenter->redirect("this");
  }
}
?>