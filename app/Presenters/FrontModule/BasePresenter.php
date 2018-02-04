<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Parent of all front presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nexendrie\Presenters\BasePresenter {
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  
  /**
   * The user must be logged in to see a page
   */
  protected function requiresLogin(): void {
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("K zobrazení této stránky musíš být přihlášen.");
      $this->redirect("User:login");
    }
  }
  
  /**
   * The user must not be logged in to see a page
   */
  protected function mustNotBeLoggedIn(): void {
    if($this->user->isLoggedIn()) {
      $this->flashMessage("Už jsi přihlášen.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * The user must not be banned to see a page
   */
  protected function mustNotBeBanned(): void {
    if($this->user->identity->banned) {
      $this->flashMessage("Ještě neskončil tvůj trest.");
      $this->redirect("Prison:");
    }
  }
  
  /**
   * The user must not be on adventure to see a page
   */
  protected function mustNotBeTavelling(): void {
    if($this->user->isLoggedIn() AND $this->user->identity->travelling) {
      $this->flashMessage("Toto nemůžeš dělat, když jsi na cestách.");
      $this->redirect("Homepage:");
    }
  }
}
?>