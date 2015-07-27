<?php
namespace Nexendrie\AdminModule\Presenters;

/**
 * Parent of all presenters
 *
 * @author Jakub Konečný
 */
class BasePresenter extends \Nette\Application\UI\Presenter {
  use \Kdyby\Autowired\AutowireProperties;
  use \Kdyby\Autowired\AutowireComponentFactories;
  
  /**
   * Check if the user is logged in, set website's style and set guest and authenticated role
   * 
   * @return void
   */
  function startup() {
    parent::startup();
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("Pro přístup do administrace musíš být přihlášený.");
      $this->redirect("Front:User:login");
    }
    if(!$this->user->isAllowed("site", "manage")) {
      $this->flashMessage("Nemáš přístup do administrace.");
      $this->redirect("Front:Homepage:");
    }
    if($this->user->identity) $this->template->style = $this->user->identity->style;
    $groupModel = $this->context->getService("model.group");
    $this->user->guestRole = $groupModel->get(GUEST_ROLE)->single_name;
    $this->user->authenticatedRole = $groupModel->get(LOGGEDIN_ROLE)->single_name;
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
      $this->redirect("Front:Homepage:");
    }
  }
}
?>