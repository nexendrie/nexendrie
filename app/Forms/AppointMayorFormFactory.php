<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Town;
use Nexendrie\Model\TownNotOwnedException;
use Nexendrie\Model\UserNotFoundException;
use Nexendrie\Model\UserDoesNotLiveInTheTownException;
use Nexendrie\Model\InsufficientLevelForMayorException;

/**
 * Factory for form AppointMayor
 *
 * @author Jakub Konečný
 */
final class AppointMayorFormFactory
{
    private \Nexendrie\Orm\Town $town;

    public function __construct(private readonly Town $model)
    {
    }

    public function create(int $townId): Form
    {
        $this->town = $this->model->get($townId);
        $form = new Form();
        $form->addSelect("mayor", "Nový rychtář:", $this->model->getTownCitizens($townId))
            ->setRequired("Vyber nového rychtáře.");
        $form->addSubmit("submit", "Jmenovat");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->model->appointMayor($this->town->id, $values["mayor"]);
        } catch (TownNotOwnedException) {
            $form->addError("Zadané město ti nepatří.");
        } catch (UserNotFoundException) {
            $form->addError("Vybarný uživatel nebyl nalezen.");
        } catch (UserDoesNotLiveInTheTownException) {
            $form->addError("Vybraný uživatel nežije ve městě.");
        } catch (InsufficientLevelForMayorException) {
            $form->addError("Vybraný uživatel nemá dostečnou úroveň.");
        }
    }
}
