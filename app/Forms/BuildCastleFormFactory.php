<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\CannotBuildCastleException,
    Nexendrie\Model\CannotBuildMoreCastlesException,
    Nexendrie\Model\CastleNameInUseException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * Factory for form BuildCastle
 *
 * @author Jakub Konečný
 */
class BuildCastleFormFactory {
  /** @var \Nexendrie\Model\Castle */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Castle $model) {
    $this->model = $model;
  }
  
  public function create(): Form {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků", 20);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Postavit");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->build($values);
    } catch(CannotBuildCastleException $e) {
      $form->addError("Nemůže stavět hrad.");
    } catch(CannotBuildMoreCastlesException $e) {
      $form->addError("Můžeš postavit jen 1 hrad.");
    } catch(CastleNameInUseException $e) {
      $form->addError("Zadané jméno je již zabrané.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    }
  }
}
?>