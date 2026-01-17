<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\Adventure;
use Nexendrie\Model\AdventureNotFoundException;
use Nexendrie\Orm\Adventure as AdventureEntity;
use Nexendrie\Forms\AddEditAdventureEnemyFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\AdventureNpcNotFoundException;
use Nexendrie\Orm\AdventureNpc as AdventureNpcEntity;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Presenter AdventureEnemy
 *
 * @author Jakub Konečný
 */
final class AdventureEnemiesPresenter extends BasePresenter
{
    private AdventureEntity $adventure;
    private AdventureNpcEntity $npc;

    public function __construct(private readonly Adventure $model)
    {
        parent::__construct();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionList(int $id): void
    {
        $this->requiresPermissions("content", "list");
        try {
            $this->template->npcs = $this->model->listOfNpcs($id);
            $this->template->adventureId = $id;
        } catch (AdventureNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionAdd(int $id): void
    {
        $this->requiresPermissions("content", "add");
        try {
            $this->adventure = $this->model->get($id);
            $this->template->adventureName = $this->adventure->name;
        } catch (AdventureNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function createComponentAddAdventureEnemyForm(AddEditAdventureEnemyFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (Form $form, array $data): void {
            $data["adventure"] = $this->adventure->id;
            $this->model->addNpc($data);
            $this->flashMessage("Nepřítel přidán.");
            $this->redirect("list", ["id" => $this->adventure->id]);
        };
        return $form;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id): void
    {
        $this->requiresPermissions("content", "edit");
        try {
            $this->npc = $this->model->getNpc($id);
        } catch (AdventureNpcNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function createComponentEditAdventureEnemyForm(AddEditAdventureEnemyFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->setDefaults($this->npc->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
        $form->onSuccess[] = function (Form $form, array $values): void {
            $this->model->editNpc((int) $this->getParameter("id"), $values);
            $this->flashMessage("Nepřítel upraven.");
            $this->redirect("list", ["id" => $this->npc->adventure->id]);
        };
        return $form;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionDelete(int $id): void
    {
        try {
            $adventure = $this->model->deleteNpc($id);
            $this->flashMessage("Nepřítel smazán.");
            $this->redirect("list", ["id" => $adventure]);
        } catch (AdventureNpcNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }
}
