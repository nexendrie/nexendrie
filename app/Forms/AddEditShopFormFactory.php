<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Market;

/**
 * Factory for form AddEditShop
 *
 * @author Jakub Konečný
 */
final class AddEditShopFormFactory {
  protected ?\Nexendrie\Orm\Shop $shop;

  public function __construct(private readonly Market $model) {
  }

  public function create(?\Nexendrie\Orm\Shop $shop = null): Form {
    $this->shop = $shop;
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 30 znaků.", 30);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    if($shop !== null) {
      $form->setDefaults($shop->toArray());
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if($this->shop === null) {
      $this->model->addShop($values);
    } else {
      $this->model->editShop($this->shop->id, $values);
    }
  }
}
?>