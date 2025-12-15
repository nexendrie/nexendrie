<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\InsufficientLevelForFoundTownException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\CannotFoundTownException;
use Nexendrie\Model\Town;
use Nexendrie\Model\TownNameInUseException;

/**
 * Factory for form FoundTown
 *
 * @author Jakub Konečný
 */
final class FoundTownFormFactory {
  public function __construct(private readonly Town $model) {
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.");
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Založit");
    $form->onSuccess[] = $this->process(...);
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->found($values);
    } catch(InsufficientLevelForFoundTownException $e) {
      $form->addError("Nejsi šlechtic.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    } catch(CannotFoundTownException $e) {
      $form->addError("Nemáš právo založit město.");
    } catch(TownNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>