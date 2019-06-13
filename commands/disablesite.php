<?php

class disablesite {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a site!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->siteExists($name)){
      msg("That user does not exist!");
      exit();
    }

    $archhost->getSite($name)->disable();
    msg("Site ".$name." disabled!");
  }

}
