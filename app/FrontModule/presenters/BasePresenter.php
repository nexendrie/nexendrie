<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Parent of all presenters
 *
 * @author Jakub Konečný
 */
class BasePresenter extends \Nette\Application\UI\Presenter {
  use \Kdyby\Autowired\AutowireProperties;
  use \Kdyby\Autowired\AutowireComponentFactories;
  
  /**
   * Set website's style and set guest and authenticated role
   * 
   * @return void
   */
  function startup() {
    parent::startup();
    if($this->user->identity) $this->template->style = $this->user->identity->style;
    $groupModel = $this->context->getService("model.group");
    $this->user->guestRole = $groupModel->get(GUEST_ROLE)->single_name;
    $this->user->authenticatedRole = $groupModel->get(LOGGEDIN_ROLE)->single_name;
    $this->template->isAdmin = $this->user->isAllowed("site", "manage");
  }
  
  /**
   * The user must be logged in to see a page
   * 
   * @return void
   */
  function requiresLogin() {
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("K zobrazení této stránky musíš být přihlášen.");
      $this->redirect("User:login");
    }
  }
  
  /**
   * The user must not be logged in to see a page
   * 
   * @return void
   */
  function mustNotBeLoggedIn() {
    if($this->user->isLoggedIn()) {
      $this->flashMessage("Už jsi přihlášen.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * The user must have specified rights to see a page
   * 
   * @param string $resource
   * @param string $action
   * @return void
   */
  function requiresPermissions($resource, $action) {
    if(!$this->user->isAllowed($resource, $action)) {
      $this->flashMessage("K zobrazení této stránky nemáš práva.");
      $this->redirect("Homepage:");
    }
  }
}
?>