<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\Mount;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form AddEditMount
 *
 * @author Jakub Konečný
 */
final class AddEditMountFormFactory
{
    private ?Mount $mount;

    public function __construct(private readonly \Nexendrie\Model\Mount $model)
    {
    }

    /**
     * @return string[]
     */
    private function getGenders(): array
    {
        return Mount::getGenders();
    }

    /**
     * @return array<int, string>
     */
    private function getMountTypes(): array
    {
        return $this->model->listOfMountTypes()->fetchPairs("id", "name"); // @phpstan-ignore return.type
    }

    public function create(?Mount $mount = null): Form
    {
        $this->mount = $mount;
        $form = new Form();
        $form->addText("name", "Jméno:")
            ->setRequired("Zadej jméno.")
            ->addRule(Form::MaxLength, "Jméno může mít maximálně 25 znaků.", 25);
        $form->addRadioList("gender", "Pohlaví:", $this->getGenders())
            ->setRequired("Vyber pohlaví.")
            ->setValue(Mount::GENDER_YOUNG);
        $form->addSelect("type", "Druh:", $this->getMountTypes())
            ->setRequired("Vyber druh.");
        $form->addInteger("price", "Cena:")
            ->setRequired("Zadej cenu.")
            ->addRule(Form::Range, "Cena musí být v rozmezí 0-999999.", [0, 999999])
            ->setValue(0);
        $form->addSubmit("submit", "Odeslat");
        $form->onSuccess[] = $this->process(...);
        if ($mount !== null) {
            $form->setDefaults($mount->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
        }
        return $form;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function process(Form $form, array $values): void
    {
        if ($this->mount === null) {
            $this->model->add($values);
        } else {
            $this->model->edit($this->mount->id, $values);
        }
    }
}
