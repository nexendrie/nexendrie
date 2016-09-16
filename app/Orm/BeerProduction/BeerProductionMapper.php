<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class BeerProductionMapper extends \Nextras\Orm\Mapper\Mapper {
  function getTableName() {
    return "beer_production";
  }
  
  /**
   * Get user's last production
   * 
   * @param int $user
   * @return BeerProduction|NULL
   */
  function getLastProduction($house) {
    return $this->builder()->where("house=$house")->orderBy("`when` DESC")->limitBy(1);
  }
}
?>