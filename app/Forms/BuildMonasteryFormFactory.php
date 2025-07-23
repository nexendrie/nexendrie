<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\CannotBuildMonasteryException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\Monastery;
use Nexendrie\Model\MonasteryNameInUseException;

/**
 * Factory for form BuildMonastery
 *
 * @author Jakub Konečný
 */
final class BuildMonasteryFormFactory {
  public function __construct(private readonly Monastery $model) {
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno");
    $form->addSubmit("submit", "Založit klášter");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->build($values["name"]);
    } catch(CannotBuildMonasteryException $e) {
      $form->addError("Nemůžeš postavit klášter.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    } catch(MonasteryNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>