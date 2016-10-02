<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\TownNotOwnedException,
    Nexendrie\Model\UserNotFoundException,
    Nexendrie\Model\UserDoesNotLiveInTheTownException,
    Nexendrie\Model\InsufficientLevelForMayorException;

/**
 * Factory for form AppointMayor
 *
 * @author Jakub Konečný
 */
class AppointMayorFormFactory {
  /** @var \Nexendrie\Model\Town */
  protected $model;
  /** @var \Nexendrie\Orm\Town */
  private $town;
  
  function __construct(\Nexendrie\Model\Town $model) {
    $this->model = $model;
  }
  
  /**
   * @param int $townId
   * @return Form
   */
  function create(int $townId): Form {
    $this->town = $this->model->get($townId);
    $form = new Form;
    $form->addSelect("mayor", "Nový rychtář:", $this->model->getTownCitizens($townId))
      ->setRequired("Vyber nového rychtáře.");
    $form->addSubmit("submit", "Jmenovat");
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values) {
    try {
      $this->model->appointMayor($this->town->id, $values["mayor"]);
    } catch(TownNotOwnedException $e) {
      $form->addError("Zadané město ti nepatří.");
    } catch(UserNotFoundException $e) {
      $form->addError("Vybarný uživatel nebyl nalezen.");
    } catch(UserDoesNotLiveInTheTownException $e) {
      $form->addError("Vybraný uživatel nežije ve městě.");
    } catch(InsufficientLevelForMayorException $e) {
      $form->addError("Vybraný uživatel nemá dostečnou úroveň.");
    }
  }
}
?>