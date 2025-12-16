<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\ItemSet;
use Nexendrie\Model\ItemSetNotFoundException;
use Nexendrie\Forms\AddEditItemSetFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Orm\ItemSet as ItemSetEntity;

/**
 * Presenter ItemSet
 *
 * @author Jakub Konečný
 */
final class ItemSetPresenter extends BasePresenter
{
    private ItemSetEntity $set;

    public function __construct(private readonly ItemSet $model)
    {
        parent::__construct();
    }

    public function actionNew(): void
    {
        $this->requiresPermissions("content", "add");
    }

    protected function createComponentAddItemSetForm(AddEditItemSetFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Sada přidána.");
            $this->redirect("Content:itemSets");
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
            $this->set = $this->model->get($id);
        } catch (ItemSetNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function createComponentEditItemSetForm(AddEditItemSetFormFactory $factory): Form
    {
        $form = $factory->create($this->set);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Sada upravena.");
            $this->redirect("Content:itemSets");
        };
        return $form;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionDelete(int $id): never
    {
        $this->requiresPermissions("content", "delete");
        try {
            $this->model->delete($id);
            $this->flashMessage("Sada smazána.");
            $this->redirect("Content:ItemSets");
        } catch (ItemSetNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }
}
