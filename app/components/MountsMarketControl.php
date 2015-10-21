<?php
namespace Nexendrie\Components;

use Nexendrie\Model\MountNotFoundException,
    Nexendrie\Model\MountNotOnSaleException,
    Nexendrie\Model\CannotBuyOwnMountException,
    Nexendrie\Model\InsufficientLevelForMountException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * MountsMarketControl
 *
 * @author Jakub Konečný
 */
class MountsMarketControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Mount */
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Mount $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/mountsMarket.latte");
    $template->mounts = $this->model->mountsOnSale();
    $template->render();
  }
  
  /**
   * @param int $mountId
   */
  function handleBuy($mountId) {
    try {
      $this->model->buy($mountId);
      $this->presenter->flashMessage("Jezdecké zvíře koupeno.");
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOnSaleException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře není na prodej.");
    } catch(CannotBuyOwnMountException $e) {
      $this->presenter->flashMessage("Toto jezdecké zvíře je již tvé.");
    } catch(InsufficientLevelForMountException $e) {
      $this->presenter->flashMessage("Nemůže si ještě koupit tento druh jezdeckého zvíře.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    }
  }
}

interface MountsMarketControlFactory {
  /** @return MountsMarketControl */
  function create();
}
?>