<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\Item;

/**
 * Factory for form AddEditItem
 *
 * @author Jakub Konečný
 */
final class AddEditItemFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  protected function getShops(): array {
    return $this->orm->shops->findAll()->fetchPairs("id", "name");
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 30 znaků.", 30);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addText("price", "Cena")
      ->setRequired("Zadej cenu.")
      ->addRule(Form::INTEGER, "Cena musí být celé číslo.")
      ->addRule(Form::RANGE, "Cena musí být v rozmezí 0-999.", [0, 999]);
    $form->addSelect("shop", "Obchod", $this->getShops())
      ->setPrompt("žádný");
    $form->addSelect("type", "Typ:", Item::getTypes())
      ->setRequired("Vyber typ.");
    $form->addText("strength", "Síla:")
      ->setRequired("Zadej síla.")
      ->addRule(Form::INTEGER, "Síla musí být celé číslo.")
      ->addRule(Form::RANGE, "Síla musí být v rozmezí 0-999.", [0, 999])
      ->setValue(0);
    $form->addSubmit("submit", "Odeslat");
    return $form;
  }
}
?>