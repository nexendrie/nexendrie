<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\ItemSet;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditItemSet
 *
 * @author Jakub Konečný
 */
final class AddEditItemSetFormFactory {
  protected \Nexendrie\Model\ItemSet $model;
  protected \Nexendrie\Orm\Model $orm;
  protected ?ItemSet $set;

  public function __construct(\Nexendrie\Model\ItemSet $model, \Nexendrie\Orm\Model $orm) {
    $this->model = $model;
    $this->orm = $orm;
  }

  protected function getWeapons(): array {
    return $this->orm->items->findWeapons()->fetchPairs("id", "name");
  }
  
  protected function getArmors(): array {
    return $this->orm->items->findArmors()->fetchPairs("id", "name");
  }
  
  protected function getHelmets(): array {
    return $this->orm->items->findHelmets()->fetchPairs("id", "name");
  }
  
  public function create(?ItemSet $set = null): Form {
    $this->set = $set;
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 30 znaků.", 30);
    $form->addSelect("weapon", "Zbraň:", $this->getWeapons())
      ->setPrompt("");
    $form->addSelect("armor", "Zbroj:", $this->getArmors())
      ->setPrompt("");
    $form->addSelect("helmet", "Přilba:", $this->getHelmets())
      ->setPrompt("");
    $form->addSelect("stat", "Vlastnost:", ItemSet::getStats())
      ->setRequired("Vyber vlastnost.");
    $form->addText("bonus", "Velikost bonusu:")
      ->setRequired("Zadej velikost bonusu.")
      ->addRule(Form::INTEGER, "Velikost bonusu musí být celé číslo.")
      ->addRule(Form::RANGE, "Velikost bonusu musí být v rozmezí 0-99.", [0, 99])
      ->setValue(0);
    $form->addSubmit("submit", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    if($set !== null) {
      $form->setDefaults($set->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if($this->set === null) {
      $this->model->add($values);
    } else {
      $this->model->edit($this->set->id, $values);
    }
  }
}
?>