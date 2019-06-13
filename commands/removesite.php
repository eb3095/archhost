<?php

class removesite {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a site!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->siteExists($name)){
      msg("That site does not exist!");
      exit();
    }

    $archhost->getSite($name)->delete();
    msg("Site ".$name." deleted!");
  }

}
