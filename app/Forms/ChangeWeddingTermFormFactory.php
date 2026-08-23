<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\DateTimeControl;
use Nexendrie\Orm\Marriage;
use Nexendrie\Orm\Model as ORM;

/**
 * Factory for form ChangeWeddingTerm
 *
 * @author Jakub Konečný
 */
final class ChangeWeddingTermFormFactory
{
    private Marriage $marriage;

    public function __construct(private readonly ORM $orm)
    {
    }

    public function create(Marriage $marriage): Form
    {
        $this->marriage = $marriage;
        $form = new Form();
        $form->addDateTime("term", "Nový termín:")
            ->setRequired("Zadej datum a čas.")
            ->setValue($marriage->term)
            ->addRule(static function (DateTimeControl $dateTimePicker): bool {
                return $dateTimePicker->value->getTimestamp() > time();
            }, "Datum nemůže být v minulosti.");
        $form->addSubmit("submit", "Změnit");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function process(Form $form, array $values): void
    {
        $this->marriage->term = $values["term"]->getTimestamp();
        $this->orm->marriages->persistAndFlush($this->marriage);
    }
}
