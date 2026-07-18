<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Group;

/**
 * Factory for form EditGroup
 *
 * @author Jakub Konečný
 */
final class EditGroupFormFactory
{
    private \Nexendrie\Orm\Group $group;

    public function __construct(private readonly Group $model, \Nette\Security\User $user)
    {
        $this->model->user = $user;
    }

    public function create(\Nexendrie\Orm\Group $group): Form
    {
        $this->group = $group;
        $form = new Form();
        $form->addText("name", "Jméno:")
            ->addRule(Form::MaxLength, "Jméno skupiny může mít maximálně 30 znaků.", 30)
            ->setRequired("Zadej jméno skupiny.");
        $form->addText("singleName", "Titul člena:")
            ->addRule(Form::MaxLength, "Titul člena může mít maximálně 30 znaků.", 30)
            ->setRequired("Zadej titul člena.");
        $form->addText("femaleName", "Titul členky:")
            ->addRule(Form::MaxLength, "Titul členky může mít maximálně 30 znaků.", 30)
            ->setRequired("Zadej titul členky.");
        $form->addInteger("level", "Úroveň skupiny:")
            ->addRule(Form::MaxLength, "Úroveň skupiny může mít maximálně 5 znaků.", 5)
            ->setRequired("Zadej úroveň skupiny.");
        $form->addInteger("maxLoan", "Maximální půjčka:")
            ->addRule(Form::MaxLength, "Maximální půjčka může mít maximálně 5 znaků.", 5)
            ->setRequired("Zadej maximální půjčku.");
        $form->addSubmit("send", "Odeslat");
        $form->onSuccess[] = $this->process(...);
        $form->setDefaults($group->toArray());
        return $form;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function process(Form $form, array $values): void
    {
        $this->model->edit($this->group->id, $values);
    }
}
