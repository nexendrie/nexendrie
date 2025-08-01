<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Mount;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditMount
 *
 * @author Jakub Konečný
 */
final class ManageMountFormFactory {
  private int $id;
  
  public function __construct(private readonly Mount $model) {
  }
  
  public function create(int $mountId): Form {
    $form = new Form();
    $this->id = $mountId;
    $mount = $this->model->get($this->id);
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addText("price", "Cena:")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999999.", [0, 999999]);
    $form->addCheckbox("onMarket", "Na prodej");
    $form->addCheckbox("autoFeed", "Automaticky krmit");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($mount->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    $this->model->edit($this->id, $values);
  }
}
?>