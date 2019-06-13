<?php

class regenerate {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a site!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->siteExists($name)){
      msg("That site doesn't exist!");
      exit();
    }

    $archhost->getSite($name)->regenerateNginxConfig();
    $archhost->getSite($name)->regeneratePHPFPMConfig();
    msg("Site regenerated!");
  }

}
