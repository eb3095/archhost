<?php

class getsitefree {

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
    $site = $archhost->getSite($name);
    $string = $site->getUsedSpace()."M / ".$site->getSize()."M - ".$site->getUsedSpacePercent()."%";
    msg("Site: ".$name." Space: ".$string);
  }

}
