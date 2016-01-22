<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\FoundGuildFormFactory,
    Nette\Application\UI\Form;

/**
 * Presenter Guild
 *
 * @author Jakub Konečný
 */
class GuildPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Guild @autowire */
  protected $model;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->guilds = $this->model->listOfGuilds();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    
  }
  
  /**
   * @return void
   */
  function actionFound() {
    if(!$this->model->canFound()) {
      $this->flashMessage("Nemůžeš založit cech.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param FoundGuildFormFactory $factory
   * @return Form
   */
  protected function createComponentFoundGuildForm(FoundGuildFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Cech založen.");
      $this->redirect("default");
    };
    return $form;
  }
}
?>