<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Market;
use Nexendrie\Orm\Item;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditItem
 *
 * @author Jakub Konečný
 */
final class AddEditItemFormFactory
{
    private ?Item $item;

    public function __construct(private readonly Market $model)
    {
    }

    /**
     * @return array<int, string>
     */
    private function getShops(): array
    {
        return $this->model->listOfShops()->fetchPairs("id", "name"); // @phpstan-ignore return.type
    }

    public function create(?Item $item = null): Form
    {
        $this->item = $item;
        $form = new Form();
        $form->addText("name", "Jméno:")
            ->setRequired("Zadej jméno.")
            ->addRule(Form::MaxLength, "Jméno může mít maximálně 30 znaků.", 30);
        $form->addTextArea("description", "Popis:")
            ->setRequired("Zadej popis.");
        $form->addInteger("price", "Cena:")
            ->setRequired("Zadej cenu.")
            ->addRule(Form::Range, "Cena musí být v rozmezí 0-999.", [0, 999]);
        $form->addSelect("shop", "Obchod:", $this->getShops())
            ->setPrompt("žádný");
        $form->addSelect("type", "Typ:", Item::getTypes())
            ->setRequired("Vyber typ.");
        $form->addInteger("strength", "Síla:")
            ->setRequired("Zadej síla.")
            ->addRule(Form::Range, "Síla musí být v rozmezí 0-999.", [0, 999])
            ->setValue(0);
        $form->addSubmit("submit", "Odeslat");
        $form->onSuccess[] = $this->process(...);
        if ($item !== null) {
            $form->setDefaults($item->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
        }
        return $form;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function process(Form $form, array $values): void
    {
        if ($this->item === null) {
            $this->model->addItem($values);
        } else {
            $this->model->editItem($this->item->id, $values);
        }
    }
}
