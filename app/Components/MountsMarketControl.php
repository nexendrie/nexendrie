<?php
declare(strict_types=1);

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
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
class MountsMarketControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Mount */
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  
  public function __construct(\Nexendrie\Model\Mount $model, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->user = $user;
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/mountsMarket.latte");
    $this->template->mounts = $this->model->mountsOnSale();
    $this->template->render();
  }
  
  public function handleBuy(int $mount): void {
    try {
      $this->model->buy($mount);
      $this->presenter->flashMessage("Jezdecké zvíře koupeno.");
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOnSaleException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře není na prodej.");
    } catch(CannotBuyOwnMountException $e) {
      $this->presenter->flashMessage("Toto jezdecké zvíře je již tvé.");
    } catch(InsufficientLevelForMountException $e) {
      $this->presenter->flashMessage("Nemůžeš si ještě koupit tento druh jezdeckého zvíře.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    }
  }
}
?>