<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\UserNotFoundException,
    Nexendrie\Model\UserDoesNotLiveInTheTownException,
    Nexendrie\Model\TooHighLevelException;

/**
 * Factory for form MakeCitizen
 *
 * @author Jakub Konečný
 */
class MakeCitizenFormFactory {
  /** @var \Nexendrie\Model\Town */
  protected $model;
  
  function __construct(\Nexendrie\Model\Town $model) {
    $this->model = $model;
  }
  
  /**
   * @param int $town
   * @return Form
   */
  function create($town): Form {
    $form = new Form;
    $form->addSelect("user", "Uživatel:", $this->model->getTownPeasants($town))
      ->setRequired();
    $form->addSubmit("submit", "Povýsit");
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values): void {
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