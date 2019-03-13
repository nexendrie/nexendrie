<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\Item;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditItem
 *
 * @author Jakub Konečný
 */
final class AddEditItemFormFactory {
  /** @var \Nexendrie\Model\Market */
  protected $model;
  /** @var \Nexendrie\Orm\Item */
  protected $item;

  public function __construct(\Nexendrie\Model\Market $model) {
    $this->model = $model;
  }

  protected function getShops(): array {
    return $this->model->listOfShops()->fetchPairs("id", "name");
  }
  
  public function create(?\Nexendrie\Orm\Item $item = null): Form {
    $this->item = $item;
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 30 znaků.", 30);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addText("price", "Cena:")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999.", [0, 999]);
    $form->addSelect("shop", "Obchod:", $this->getShops())
      ->setPrompt("žádný");
    $form->addSelect("type", "Typ:", Item::getTypes())
      ->setRequired("Vyber typ.");
    $form->addText("strength", "Síla:")
      ->setRequired("Zadej síla.")
      ->addRule(Form::INTEGER, "Síla musí být celé číslo.")
      ->addRule(Form::RANGE, "Síla musí být v rozmezí 0-999.", [0, 999])
      ->setValue(0);
    $form->addSubmit("submit", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    if(!is_null($item)) {
      $form->setDefaults($item->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if(is_null($this->item)) {
      $this->model->addItem($values);
    } else {
      $this->model->editItem($this->item->id, $values);
    }
  }
}
?>