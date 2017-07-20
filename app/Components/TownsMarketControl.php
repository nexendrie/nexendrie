<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\TownNotFoundException,
    Nexendrie\Model\TownNotOnSaleException,
    Nexendrie\Model\CannotBuyOwnTownException,
    Nexendrie\Model\InsufficientLevelForTownException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * TownsMarketControl
 *
 * @author Jakub Konečný
 */
class TownsMarketControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Town */
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Town $model, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->user = $user;
  }
  
  function render(): void {
    $this->template->setFile(__DIR__ . "/townsMarket.latte");
    $this->template->towns = $this->model->townsOnSale();
    $this->template->render();
  }
  
  function handleBuy(int $townId): void {
    try {
      $this->model->buy($townId);
      $this->presenter->flashMessage("Město koupeno.");
    } catch(TownNotFoundException $e) {
      $this->presenter->flashMessage("Město nenalezeno.");
    } catch(TownNotOnSaleException $e) {
      $this->presenter->flashMessage("Město není na prodej.");
    } catch(CannotBuyOwnTownException $e) {
      $this->presenter->flashMessage("Toto město je již tvé.");
    } catch(InsufficientLevelForTownException $e) {
      $this->presenter->flashMessage("Nemůžeš kupovat města.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    }
  }
}
?>