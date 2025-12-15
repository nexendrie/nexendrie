<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Town;
use Nexendrie\Model\UserNotFoundException;
use Nexendrie\Model\UserDoesNotLiveInTheTownException;
use Nexendrie\Model\TooHighLevelException;

/**
 * Factory for form MakeCitizen
 *
 * @author Jakub Konečný
 */
final class MakeCitizenFormFactory {
  public function __construct(private readonly Town $model) {
  }
  
  public function create(int $town): Form {
    $form = new Form();
    $form->addSelect("user", "Uživatel:", $this->model->getTownPeasants($town))
      ->setRequired();
    $form->addSubmit("submit", "Povýsit");
    $form->onSuccess[] = $this->process(...);
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->makeCitizen($values["user"]);
    } catch(UserNotFoundException) {
      $form->addError("Zadaný uživatel neexistuje.");
    } catch(UserDoesNotLiveInTheTownException) {
      $form->addError("Vybraný uživatel nežije ve tvém městě.");
    } catch(TooHighLevelException) {
      $form->addError("Vybraný uživatel už není sedlák.");
    }
  }
}
?>