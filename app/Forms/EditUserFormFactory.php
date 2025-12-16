<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\UserManager;
use Nexendrie\Model\Town;
use Nexendrie\Orm\Model as ORM;

/**
 * Factory for form EditUser
 *
 * @author Jakub Konečný
 */
final class EditUserFormFactory
{
    private int $uid;

    public function __construct(
        private readonly ORM $orm,
        private readonly UserManager $model,
        private readonly Town $townModel
    ) {
    }

    private function getListOfGroups(int $uid): array
    {
        if ($uid === 0) {
            $groups = $this->orm->groups->findBy(["id" => 0]);
        } else {
            $groups = $this->orm->groups->findBy(["level>" => 0, "id!=" => 0]);
        }
        return $groups->fetchPairs("id", "name");
    }

    private function getListOfTowns(): array
    {
        return $this->townModel->listOfTowns()->fetchPairs("id", "name");
    }

    /**
     * @throws \Nette\ArgumentOutOfRangeException
     */
    private function getDefaultValues(): array
    {
        $user = $this->orm->users->getById($this->uid);
        if ($user === null) {
            throw new \Nette\ArgumentOutOfRangeException("User with specified id does not exist.");
        }
        return [
            "publicname" => $user->publicname,
            "group" => $user->group->id,
            "town" => $user->town->id
        ];
    }

    public function create(int $uid): Form
    {
        $form = new Form();
        $this->uid = $uid;
        $groups = $this->getListOfGroups($uid);
        $form->addText("publicname", "Zobrazované jméno:")
            ->setRequired("Zobrazované jméno nesmí být prázdné");
        $form->addSelect("group", "Skupina:", $groups)
            ->setRequired("Vyber skupinu.");
        $form->addSelect("town", "Město", $this->getListOfTowns())
            ->setRequired("Vyber město.");
        $form->setDefaults($this->getDefaultValues());
        $form->addSubmit("submit", "Uložit");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        $this->model->edit($this->uid, $values);
    }
}
