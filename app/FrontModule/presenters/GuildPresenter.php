<?php
namespace Nexendrie\Presenters\FrontModule;

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
  function renderList() {
    $this->template->guilds = $this->model->listOfGuilds();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    
  }
}
?>