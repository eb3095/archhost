<?php

class getpoolsfree {

  public function run($archhost, $argv){
    $pools = $archhost->getPools();
    foreach ($pools as $pool){
      $string = $pool->getUsedSpace()."M / ".$pool->getTotalSpace()."M - ".$pool->getUsedPercent()."%";
      msg("Pool: ".$pool->getName()." Space: ".$string);
    }
  }

}
