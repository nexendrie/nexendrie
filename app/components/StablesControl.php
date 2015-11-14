<?php
namespace Nexendrie\Components;

use Nexendrie\Model\MountNotFoundException,
    Nexendrie\Model\MountNotOwnedException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\CareNotNeededException;

/**
 * StablesControl
 *
 * @author Jakub Konečný
 */
class StablesControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/stables.latte");
    $template->mounts = $this->orm->mounts->findByOwner($this->user->id);
    $template->render();
  }
  
  /**
   * Increase specified mount's life
   * 
   * @param int $mountId
   * @param int $hp
   * @param int $price
   * @return void
   * @throws MountNotFoundException
   * @throws MountNotOwnedException
   * @throws InsufficientFundsException
   * @throws CareNotNeededException
   */
  protected function increaseLife($mountId, $hp, $price) {
    $mount = $this->orm->mounts->getById($mountId);
    if(!$mount) throw new MountNotFoundException;
    if($mount->owner->id != $this->user->id) throw new MountNotOwnedException;
    if($mount->owner->money < $price) throw new InsufficientFundsException;
    if($mount->hp >= 100) throw new CareNotNeededException;
    $mount->hp += $hp;
    $mount->owner->money -= $price;
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  /**
   * @param int $mountId
   * @return void
   */
  function handleCare($mountId) {
    try {
      $this->increaseLife($mountId, 3, 4);
      $this->presenter->flashMessage("Očistil jsi jezdecké zvíře.");
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOwnedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    } catch(CareNotNeededException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře nepotřebuje čištění.");
    }
  }
  
  /**
   * @param int $mountId
   * @return void
   */
  function handleFeed($mountId) {
    try {
      $this->increaseLife($mountId, 10, 12);
      $this->presenter->flashMessage("Nakrmil jsi jezdecké zvíře.");
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOwnedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    } catch(CareNotNeededException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře nepotřebuje krmení.");
    }
  }
}

interface StablesControlFactory {
  /** @return StablesControl */
  function create();
}
?>