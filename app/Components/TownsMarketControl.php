<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\TownNotFoundException;
use Nexendrie\Model\TownNotOnSaleException;
use Nexendrie\Model\CannotBuyOwnTownException;
use Nexendrie\Model\CannotBuyTownException;
use Nexendrie\Model\InsufficientFundsException;

/**
 * TownsMarketControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class TownsMarketControl extends \Nette\Application\UI\Control {
  protected \Nexendrie\Model\Town $model;
  protected \Nette\Security\User $user;
  
  public function __construct(\Nexendrie\Model\Town $model, \Nette\Security\User $user, IUserProfileLinkControlFactory $userProfileLinkControlFactory) {
    $this->model = $model;
    $this->user = $user;
    $this->addComponent($userProfileLinkControlFactory->create(), "userProfileLink");
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/townsMarket.latte");
    $this->template->towns = $this->model->townsOnSale();
    $this->template->render();
  }
  
  public function handleBuy(int $town): void {
    try {
      $this->model->buy($town);
      $this->presenter->flashMessage("Město koupeno.");
    } catch(TownNotFoundException $e) {
      $this->presenter->flashMessage("Město nenalezeno.");
    } catch(TownNotOnSaleException $e) {
      $this->presenter->flashMessage("Město není na prodej.");
    } catch(CannotBuyOwnTownException $e) {
      $this->presenter->flashMessage("Toto město je již tvé.");
    } catch(CannotBuyTownException $e) {
      $this->presenter->flashMessage("Nemůžeš kupovat města.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    }
  }
}
?>