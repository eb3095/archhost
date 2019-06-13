<?php

class getpoolfree {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a pool!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->poolExists($name)){
      msg("That pool does not exist!");
      exit();
    }
    $pool = $archhost->getPool($name);
    $string = $pool->getUsedSpace()."M / ".$pool->getTotalSpace()."M - ".$pool->getUsedPercent()."%";
    msg("Pool: ".$name." Space: ".$string);
  }

}
