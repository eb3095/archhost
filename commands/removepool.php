<?php

class removepool {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a pool!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->poolExists($name)){
      msg("That pool doesn't exist!");
      exit();
    }

    $archhost->removePool($name);
    msg("Pool ".$name." removed!");
  }

}
