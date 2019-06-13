<?php

class getsitesfree {

  public function run($archhost, $argv){
    $sites = $archhost->getSites();
    foreach ($sites as $site){
      $string = $site->getUsedSpace()."M / ".$site->getSize()."M - ".$site->getUsedSpacePercent()."%";
      msg("Site: ".$site->getName()." Space: ".$string);
    }
  }

}
