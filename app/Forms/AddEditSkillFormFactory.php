<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Skills;
use Nexendrie\Orm\Skill as SkillEntity;

/**
 * Factory for form AddEditSkill
 *
 * @author Jakub Konečný
 */
final class AddEditSkillFormFactory
{
    private ?SkillEntity $skill;

    public function __construct(private readonly Skills $model)
    {
    }

    public function create(?SkillEntity $skill = null): Form
    {
        $this->skill = $skill;
        $form = new Form();
        $form->addText("name", "Jméno:")
            ->setRequired("Zadej jméno.")
            ->addRule(Form::MaxLength, "Jméno může mít maximálně 20 znaků.", 20);
        $form->addInteger("price", "Cena:")
            ->setRequired("Zadej cenu.")
            ->addRule(Form::Range, "Cena musí být v rozmezí 1-999.", [1, 999])
            ->setOption("description", "Cena na první úrovni");
        $form->addInteger("maxLevel", "Úrovní:")
            ->setRequired("Zadej počet úrovní.")
            ->addRule(Form::Range, "Počet úrovní musí být v rozmezí 1-99.", [1, 99])
            ->setValue(5);
        $type = $form->addSelect("type", "Typ:", SkillEntity::getTypes())
            ->setRequired("Vyber typ.");
        $form->addSelect("stat", "Vlastnost:", SkillEntity::getStats())
            ->setPrompt("žádná")
            ->addConditionOn($type, Form::Equal, SkillEntity::TYPE_COMBAT)
            ->setRequired("Vyber vlastnost.")
            ->elseCondition()
            ->addRule(Form::Blank, "Neplatná kombinace: vybrána vlastnost u pracovní dovednosti.");
        $form->addInteger("statIncrease", "Vylepšení vlastnosti:")
            ->setValue(0)
            ->setRequired()
            ->addConditionOn($type, Form::Equal, SkillEntity::TYPE_COMBAT)
            ->addRule(Form::Range, "Vylepšení vlastnosti musí být v rozmezí 1-99.", [1, 99])
            ->elseCondition()
            ->addRule(Form::Equal, "Neplatná kombinace: vylepšení dovednosti musí být 0 u pracovní dovednosti.", 0);
        $form->addSubmit("submit", "Odeslat");
        $form->onSuccess[] = $this->process(...);
        if ($skill !== null) {
            $form->setDefaults($skill->toArray());
        }
        return $form;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function process(Form $form, array $values): void
    {
        if ($this->skill === null) {
            $this->model->add($values);
        } else {
            $this->model->edit($this->skill->id, $values);
        }
    }
}
