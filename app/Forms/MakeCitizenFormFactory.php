<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\UserNotFoundException;
use Nexendrie\Model\UserDoesNotLiveInTheTownException;
use Nexendrie\Model\TooHighLevelException;

/**
 * Factory for form MakeCitizen
 *
 * @author Jakub Konečný
 */
final class MakeCitizenFormFactory {
  protected \Nexendrie\Model\Town $model;
  
  public function __construct(\Nexendrie\Model\Town $model) {
    $this->model = $model;
  }
  
  public function create(int $town): Form {
    $form = new Form();
    $form->addSelect("user", "Uživatel:", $this->model->getTownPeasants($town))
      ->setRequired();
    $form->addSubmit("submit", "Povýsit");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->makeCitizen($values["user"]);
    } catch(UserNotFoundException $e) {
      $form->addError("Zadaný uživatel neexistuje.");
    } catch(UserDoesNotLiveInTheTownException $e) {
      $form->addError("Vybraný uživatel nežije ve tvém městě.");
    } catch(TooHighLevelException $e) {
      $form->addError("Vybraný uživatel už není sedlák.");
    }
  }
}
?>