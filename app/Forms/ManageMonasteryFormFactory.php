<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Monastery;
use Nexendrie\Model\MonasteryNotFoundException;
use Nexendrie\Model\MonasteryNameInUseException;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form ManageMonastery
 *
 * @author Jakub Konečný
 */
final class ManageMonasteryFormFactory
{
    private int $id;

    public function __construct(private readonly Monastery $model)
    {
    }

    public function create(int $id): Form
    {
        $this->id = $id;
        $form = new Form();
        $form->addText("name", "Jméno:")
            ->setRequired("Zadej jméno");
        $form->addSelect("leader", "Vůdce:", $this->model->highClerics($id));
        $form->addSubmit("submit", "Odeslat");
        $form->setDefaults($this->model->get($id)->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->model->edit($this->id, $values);
        } catch (MonasteryNotFoundException) {
            $form->addError("Neplatný klášter.");
        } catch (MonasteryNameInUseException) {
            $form->addError("Zadané jméno je již zabráno.");
        }
    }
}
